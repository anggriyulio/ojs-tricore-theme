<?php

/**
 * @file plugins/themes/tricore/TricoreThemePlugin.inc.php
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @class TricoreThemePlugin
 *
 * @brief Tricore: parent theme of the Core Series (OJS 3.3 adapter).
 *
 * Child themes extend it with $this->setParent('tricorethemeplugin'). Logic that
 * does not depend on OJS lives in classes/ and is shared unchanged with the
 * OJS 3.4 and 3.5 branches; only this adapter differs between versions.
 */

import('lib.pkp.classes.plugins.ThemePlugin');

require_once __DIR__ . '/classes/Color.php';
require_once __DIR__ . '/classes/OptionSanitizer.php';
require_once __DIR__ . '/classes/OptionDefinitions.php';
require_once __DIR__ . '/classes/Tokens.php';
require_once __DIR__ . '/classes/PageContext.php';
require_once __DIR__ . '/classes/Metrics.php';
require_once __DIR__ . '/classes/SiteStats.php';

use Illuminate\Database\Capsule\Manager as Capsule;

use Tricore\Theme\Color;
use Tricore\Theme\Metrics;
use Tricore\Theme\OptionDefinitions;
use Tricore\Theme\OptionSanitizer;
use Tricore\Theme\PageContext;
use Tricore\Theme\SiteStats;
use Tricore\Theme\Tokens;

class TricoreThemePlugin extends ThemePlugin {

	/** Name of the main stylesheet. Child themes modify it with modifyStyle(). */
	const STYLESHEET = 'tricore-stylesheet';

	/** Prefix of the heading fields that group options in the settings form */
	const GROUP_FIELD_PREFIX = 'tricoreGroup';

	/** @var bool Hooks are registered once even though init() can run several times */
	private $hooksRegistered = false;

	/** Metric type of the OJS 3.3 COUNTER statistics in the metrics table */
	const METRIC_TYPE = 'ojs::counter';

	/** Submission status "published" (STATUS_PUBLISHED, same value in all versions) */
	const STATUS_PUBLISHED = 3;

	/** @var array<int, array> Views/downloads per submission id, cached for the request */
	private $metricsCache = [];

	/**
	 * @copydoc ThemePlugin::init()
	 */
	public function init() {
		AppLocale::requireComponents(LOCALE_COMPONENT_PKP_MANAGER, LOCALE_COMPONENT_APP_MANAGER);

		$this->registerOptions();

		// Base layer: the default theme stylesheet of this OJS version (styles
		// every frontend page), then the Tricore design layer. See styles/index.less.
		$this->addStyle(self::STYLESHEET, 'styles/index.less');

		// Fonts bundled with the default theme, self-hosted (no CDN)
		$fonts = [
			'notoSans' => 'notoSans.less',
			'notoSerif_notoSans' => 'notoSans_notoSerif.less',
			'lora_openSans' => 'lora_openSans.less',
			'lato' => 'lato.less',
		];
		$typography = $this->getOption('typography');
		if (isset($fonts[$typography])) {
			$this->addStyle('tricore-font', '../default/styles/fonts/' . $fonts[$typography]);
		}

		$request = Application::get()->getRequest();
		$this->addStyle(
			'fontAwesome',
			$request->getBaseUrl() . '/lib/pkp/styles/fontawesome/fontawesome.css',
			['baseUrl' => '']
		);

		// jQuery stays for compatibility with block plugins and the
		// registration form (reviewer interests); Tricore's own script is vanilla JS.
		$min = Config::getVar('general', 'enable_minified') ? '.min' : '';
		$this->addScript('jQuery', $request->getBaseUrl() . '/lib/pkp/lib/vendor/components/jquery/jquery' . $min . '.js', ['baseUrl' => '']);
		$this->addScript('jQueryUI', $request->getBaseUrl() . '/lib/pkp/lib/vendor/components/jqueryui/jquery-ui' . $min . '.js', ['baseUrl' => '']);
		$this->addScript('tricore-main', 'js/main.js');

		$this->addMenuArea(['primary', 'user']);

		if (!$this->hooksRegistered) {
			HookRegistry::register('PageHandler::compileLess', [$this, 'addTokensToLess']);
			HookRegistry::register('TemplateManager::display', [$this, 'loadTemplateData']);
			HookRegistry::register('Templates::Issue::Issue::Article', [$this, 'appendArticleMetrics']);
			HookRegistry::register('Templates::Article::Details', [$this, 'appendArticleDetailsMetrics']);
			$this->hooksRegistered = true;
		}
	}

	/**
	 * Register the theme options from OptionDefinitions, preceded by a
	 * heading field for each group.
	 */
	protected function registerOptions() {
		$currentGroup = null;
		foreach (OptionDefinitions::all() as $name => $option) {
			if ($option['group'] !== $currentGroup) {
				$currentGroup = $option['group'];
				$this->addOption(self::GROUP_FIELD_PREFIX . ucfirst($currentGroup), 'FieldHTML', [
					'label' => __(OptionDefinitions::PREFIX . 'group.' . $currentGroup),
					'description' => '',
				]);
			}

			$args = [
				'label' => __($option['label']),
				'default' => $option['default'],
			];
			if (isset($option['description'])) {
				$args['description'] = __($option['description']);
			}
			if (!empty($option['multilingual'])) {
				$args['isMultilingual'] = true;
			}
			if (isset($option['optionType'])) {
				$args['type'] = $option['optionType'];
			}
			if (isset($option['options'])) {
				$args['options'] = array_map(function ($choice) {
					$label = (string) $choice['label'];
					return [
						'value' => $choice['value'],
						'label' => strpos($label, OptionDefinitions::PREFIX) === 0 ? __($label) : $label,
					];
				}, $option['options']);
			}
			$this->addOption($name, $option['type'], $args);
		}
	}

	/**
	 * @copydoc Plugin::getDisplayName()
	 */
	public function getDisplayName() {
		return __('plugins.themes.tricore.name');
	}

	/**
	 * @copydoc Plugin::getDescription()
	 */
	public function getDescription() {
		return __('plugins.themes.tricore.description');
	}

	/**
	 * @copydoc Plugin::getContextSpecificPluginSettingsFile()
	 */
	public function getContextSpecificPluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	/**
	 * Validate option values before they are saved.
	 *
	 * Child themes that override this must merge the result of
	 * $this->parent->validateOptions().
	 *
	 * @copydoc ThemePlugin::validateOptions()
	 */
	public function validateOptions($options, $themePluginPath, $contextId, $request) {
		$errors = [];

		foreach (OptionDefinitions::namesOfType('FieldColor') as $name) {
			if (!isset($options[$name]) || $options[$name] === '') {
				continue;
			}
			if (!Color::isValidHex($options[$name])) {
				$errors[$name] = [__('plugins.themes.tricore.error.colour')];
			}
		}

		foreach (['ctaPrimaryUrl', 'ctaSecondaryUrl'] as $name) {
			if (!empty($options[$name]) && OptionSanitizer::url($options[$name]) === '') {
				$errors[$name] = [__('plugins.themes.tricore.error.url')];
			}
		}
		foreach (OptionDefinitions::SOCIAL_NETWORKS as $network) {
			$name = 'social' . $network;
			if (!empty($options[$name]) && OptionSanitizer::url($options[$name], false) === '') {
				$errors[$name] = [__('plugins.themes.tricore.error.absoluteUrl')];
			}
		}

		return $errors;
	}

	/**
	 * Sanitize values on save, even when validation was bypassed (for
	 * example by a direct API call from a child theme).
	 *
	 * @copydoc ThemePlugin::saveOption()
	 */
	public function saveOption($name, $value, $contextId = null) {
		if (strpos($name, self::GROUP_FIELD_PREFIX) === 0) {
			return;
		}
		if (in_array($name, OptionDefinitions::namesOfType('FieldColor')) && !Color::isValidHex($value)) {
			$value = '';
		}
		if (in_array($name, ['ctaPrimaryUrl', 'ctaSecondaryUrl'])) {
			$value = OptionSanitizer::url($value);
		}
		if ($name === 'sidebarPages') {
			$value = PageContext::sanitizePages($value);
		}
		if ($name === 'indexingInfo' && is_array($value)) {
			$value = array_map([OptionSanitizer::class, 'richText'], $value);
		}
		if (strpos($name, 'social') === 0 && is_string($value)) {
			$value = OptionSanitizer::url($value, false);
		}
		parent::saveOption($name, $value, $contextId);
	}

	/**
	 * Current value of every token-producing option and the live defaults.
	 *
	 * Read from the option configs at compile time, so defaults changed by a
	 * child theme (modifyOptionsConfig) and options it removed are honoured.
	 *
	 * @return array [values, defaults]
	 */
	protected function getTokenInputs() {
		$values = $defaults = [];
		$names = array_merge(array_keys(Tokens::COLOURS), ['borderRadius', 'containerWidth', 'typography']);
		foreach ($names as $name) {
			$config = $this->getOptionConfig($name);
			if (!$config) {
				continue;
			}
			$defaults[$name] = $config->default;
			$values[$name] = $this->getOption($name);
		}
		return [$values, $defaults];
	}

	/**
	 * Append the design tokens to the Tricore stylesheet just before it is
	 * compiled, after every theme in the chain has finished init().
	 *
	 * Hooked to `PageHandler::compileLess`.
	 *
	 * @param string $hookName
	 * @param array $params [Less_Parser, lessFile, args, name, request]
	 * @return bool
	 */
	public function addTokensToLess($hookName, $params) {
		$args =& $params[2];
		$name = $params[3];
		if ($name !== self::STYLESHEET) {
			return false;
		}
		list($values, $defaults) = $this->getTokenInputs();
		$tokens = Tokens::toLess(Tokens::resolve($values, $defaults));
		$args['addLessVariables'] = array_merge(
			isset($args['addLessVariables']) ? (array) $args['addLessVariables'] : [],
			[$tokens]
		);
		return false;
	}

	/**
	 * Provide sanitized option data to the frontend templates as $tricoreTheme.
	 *
	 * Hooked to `TemplateManager::display`.
	 *
	 * @param string $hookName
	 * @param array $params [TemplateManager, template, output]
	 * @return bool
	 */
	public function loadTemplateData($hookName, $params) {
		$templateMgr = $params[0];
		$template = $params[1];
		// Not limited to frontend/ templates: plugin pages such as Static
		// Pages render the shared header and footer under their own names.
		if (!$this->isThemeInChain()) {
			return false;
		}

		$request = Application::get()->getRequest();
		$context = $request->getContext();

		$social = [];
		foreach (OptionDefinitions::SOCIAL_NETWORKS as $network) {
			$url = OptionSanitizer::url($this->getOption('social' . $network), false);
			if ($url !== '') {
				$social[] = ['network' => strtolower($network), 'label' => $network, 'url' => $url];
			}
		}

		// Sidebar per page type (theme option). Only ever forces full width,
		// so a template that already asked for full width keeps it.
		$pageKey = PageContext::pageKey($request->getRequestedPage(), $request->getRequestedOp(), (string) $template);
		if (PageContext::hideSidebar($pageKey, $this->getOption('sidebarPages'))) {
			$templateMgr->assign('isFullWidth', true);
		}

		$data = [
			'headerLayout' => OptionSanitizer::choice($this->getOption('headerLayout'), OptionDefinitions::HEADER_LAYOUTS, 'left'),
			'social' => $social,
			'pageKey' => $pageKey,
			'showMetrics' => $this->showMetrics(),
		];

		if ($pageKey === PageContext::SITE) {
			$data['site'] = $this->getSiteStats($templateMgr->getTemplateVars('journals'));
		}

		if ($context && $template === 'frontend/pages/indexJournal.tpl') {
			$data = array_merge($data, $this->getHomepageData($request, $context));
		}

		if ($context && $this->showMetrics()) {
			// Fetch the metrics of every article on an issue page in one query
			$sections = $templateMgr->getTemplateVars('publishedSubmissions');
			if (is_array($sections)) {
				$ids = [];
				foreach ($sections as $section) {
					foreach (isset($section['articles']) ? $section['articles'] : [] as $article) {
						$ids[] = $article->getId();
					}
				}
				$this->getSubmissionMetrics($ids);
			}
			if ($template === 'frontend/pages/issueArchive.tpl') {
				$templateMgr->assign('tricoreIssueMetrics', $this->getIssueMetrics($context, $templateMgr->getTemplateVars('issues')));
			}
		}

		$templateMgr->assign('tricoreTheme', $data);
		return false;
	}

	/**
	 * Localized value of a multilingual option: the first non-empty value in
	 * locale precedence order, or null.
	 *
	 * Safer than ThemePlugin::getLocalizedOption(), which indexes an empty
	 * array when nothing was saved.
	 *
	 * @param string $name
	 * @return string|null
	 */
	protected function getLocalizedCoreOption($name) {
		$value = $this->getOption($name);
		if (!is_array($value)) {
			return null;
		}
		foreach (AppLocale::getLocalePrecedence() as $locale) {
			if (isset($value[$locale]) && is_string($value[$locale]) && trim($value[$locale]) !== '') {
				return $value[$locale];
			}
		}
		return null;
	}

	/**
	 * Data for the journal homepage blocks.
	 *
	 * @param PKPRequest $request
	 * @param Context $context
	 * @return array
	 */
	protected function getHomepageData($request, $context) {
		$dispatcher = $request->getDispatcher();

		$ctas = [];
		$ctaDefaults = [
			'ctaPrimary' => [__('journal.currentIssue'), $dispatcher->url($request, ROUTE_PAGE, null, 'issue', 'current')],
			'ctaSecondary' => [__('about.submissions'), $dispatcher->url($request, ROUTE_PAGE, null, 'about', 'submissions')],
		];
		foreach ($ctaDefaults as $key => list($defaultLabel, $defaultUrl)) {
			$url = OptionSanitizer::url($this->getOption($key . 'Url'));
			$label = $this->getLocalizedCoreOption($key . 'Label');
			$ctas[] = [
				'label' => is_string($label) && trim($label) !== '' ? $label : $defaultLabel,
				'url' => $url !== '' ? $url : $defaultUrl,
				'isPrimary' => $key === 'ctaPrimary',
			];
		}

		$count = OptionSanitizer::choice((int) $this->getOption('latestArticlesCount'), OptionDefinitions::LATEST_COUNTS, 6);

		return [
			'showHero' => (bool) $this->getOption('showHero'),
			'heroTitle' => $this->getLocalizedCoreOption('heroTitle'),
			'heroSubtitle' => $this->getLocalizedCoreOption('heroSubtitle'),
			'ctas' => $ctas,
			'showDescription' => (bool) $this->getOption('showDescription'),
			'showCurrentIssue' => (bool) $this->getOption('showCurrentIssue'),
			'metrics' => OptionSanitizer::metrics($this->getLocalizedCoreOption('metrics')),
			'indexingInfo' => OptionSanitizer::richText($this->getLocalizedCoreOption('indexingInfo')),
			'latestArticles' => $count ? $this->getLatestArticles($request, $context, $count) : [],
		];
	}

	/**
	 * Most recently published articles as plain arrays, so the template does
	 * not depend on version-specific Submission/Publication methods.
	 *
	 * @param PKPRequest $request
	 * @param Context $context
	 * @param int $count
	 * @return array
	 */
	protected function getLatestArticles($request, $context, $count) {
		import('classes.submission.SubmissionDAO'); // ORDERBY_DATE_PUBLISHED
		$submissions = Services::get('submission')->getMany([
			'contextId' => $context->getId(),
			'status' => STATUS_PUBLISHED,
			'orderBy' => ORDERBY_DATE_PUBLISHED,
			'orderDirection' => 'DESC',
			'count' => $count,
		]);

		$dispatcher = $request->getDispatcher();
		$articles = [];
		foreach ($submissions as $submission) {
			$publication = $submission->getCurrentPublication();
			if (!$publication) {
				continue;
			}
			$articles[] = [
				'id' => $submission->getId(),
				'title' => $publication->getLocalizedFullTitle(),
				'authors' => $publication->getShortAuthorString(),
				'datePublished' => $publication->getData('datePublished'),
				'url' => $dispatcher->url($request, ROUTE_PAGE, null, 'article', 'view', $submission->getBestId()),
			];
		}
		if ($articles && $this->showMetrics()) {
			$metrics = $this->getSubmissionMetrics(array_column($articles, 'id'));
			foreach ($articles as &$article) {
				$article['metrics'] = Metrics::present($metrics[$article['id']]);
			}
		}
		return $articles;
	}

	/**
	 * Whether views and downloads are shown (theme option).
	 *
	 * @return bool
	 */
	protected function showMetrics() {
		return (bool) $this->getOption('showMetrics');
	}

	/**
	 * Views and downloads per submission, cached for the request.
	 *
	 * @param int[] $submissionIds
	 * @return array<int, array{views: int, downloads: int}>
	 */
	public function getSubmissionMetrics(array $submissionIds) {
		$ids = array_values(array_unique(array_map('intval', $submissionIds)));
		$missing = array_values(array_diff($ids, array_keys($this->metricsCache)));
		if ($missing) {
			$this->metricsCache += Metrics::summarize($this->queryMetricRows($missing), $missing);
		}
		return array_intersect_key($this->metricsCache, array_flip($ids));
	}

	/**
	 * Summed metric rows (submission_id, assoc_type, total) of the OJS 3.3
	 * COUNTER statistics (table `metrics`).
	 *
	 * @param int[] $submissionIds
	 * @return iterable
	 */
	protected function queryMetricRows(array $submissionIds) {
		return Capsule::table('metrics')
			->select('submission_id', 'assoc_type')
			->selectRaw('SUM(metric) AS total')
			->where('metric_type', self::METRIC_TYPE)
			->whereIn('submission_id', $submissionIds)
			->whereIn('assoc_type', Metrics::ASSOC_TYPES)
			->groupBy('submission_id', 'assoc_type')
			->get();
	}

	/**
	 * Total views and downloads of the published articles of each issue.
	 *
	 * @param Context $context
	 * @param iterable|null $issues
	 * @return array<int, array{views: int, downloads: int}> keyed by issue id
	 */
	protected function getIssueMetrics($context, $issues) {
		$issueIds = [];
		foreach ($issues ?: [] as $issue) {
			$issueIds[] = (int) $issue->getId();
		}
		if (!$issueIds) {
			return [];
		}
		$byIssue = array_fill_keys($issueIds, []);
		import('lib.pkp.classes.submission.PKPSubmission'); // STATUS_PUBLISHED
		$submissions = Services::get('submission')->getMany([
			'contextId' => $context->getId(),
			'issueIds' => $issueIds,
			'status' => STATUS_PUBLISHED,
		]);
		foreach ($submissions as $submission) {
			$publication = $submission->getCurrentPublication();
			$issueId = $publication ? (int) $publication->getData('issueId') : 0;
			if (isset($byIssue[$issueId])) {
				$byIssue[$issueId][] = (int) $submission->getId();
			}
		}
		$metrics = $this->getSubmissionMetrics(array_merge([], ...array_values($byIssue)));
		$totals = [];
		foreach ($byIssue as $issueId => $submissionIds) {
			$totals[$issueId] = Metrics::present(Metrics::sum(array_intersect_key($metrics, array_flip($submissionIds))));
		}
		return $totals;
	}

	/**
	 * Views and downloads under each article of a list (issue table of
	 * contents, search results, homepage current issue).
	 *
	 * Hooked to `Templates::Issue::Issue::Article` (end of article_summary.tpl).
	 *
	 * @param string $hookName
	 * @param array $params [params, Smarty template, output]
	 * @return bool
	 */
	public function appendArticleMetrics($hookName, $params) {
		return $this->appendMetricsPartial($params, 'frontend/components/tricore/articleMetrics.tpl');
	}

	/**
	 * Views and downloads in the details column of the article page.
	 *
	 * Hooked to `Templates::Article::Details`.
	 *
	 * @param string $hookName
	 * @param array $params [params, Smarty template, output]
	 * @return bool
	 */
	public function appendArticleDetailsMetrics($hookName, $params) {
		return $this->appendMetricsPartial($params, 'frontend/components/tricore/articleDetailsMetrics.tpl');
	}

	/**
	 * Render a metrics partial for the article in the calling template.
	 *
	 * @param array $params hook parameters
	 * @param string $partial template to render
	 * @return bool
	 */
	protected function appendMetricsPartial($params, $partial) {
		if (!$this->isThemeInChain() || !$this->showMetrics()) {
			return false;
		}
		$article = $params[1]->getTemplateVars('article');
		if (!is_object($article) || !method_exists($article, 'getId')) {
			return false;
		}
		$id = (int) $article->getId();
		$metrics = $this->getSubmissionMetrics([$id]);
		$templateMgr = TemplateManager::getManager(Application::get()->getRequest());
		$templateMgr->assign('tricoreArticleMetrics', Metrics::present($metrics[$id]));
		$output =& $params[2];
		$output .= $templateMgr->fetch($partial);
		return false;
	}

	/**
	 * Journals, published issues and published articles of the site, in
	 * total and per journal (site landing page).
	 *
	 * @param iterable|null $journals journals shown on the site homepage
	 * @return array SiteStats::summarize()
	 */
	protected function getSiteStats($journals) {
		$journalIds = [];
		foreach ($journals ?: [] as $journal) {
			$journalIds[] = (int) $journal->getId();
		}
		if (!$journalIds) {
			return SiteStats::summarize([], [], []);
		}
		$issues = Capsule::table('issues')
			->select('journal_id AS context_id')
			->selectRaw('COUNT(*) AS total')
			->whereIn('journal_id', $journalIds)
			->where('published', 1)
			->groupBy('journal_id')
			->get();
		$articles = Capsule::table('submissions')
			->select('context_id')
			->selectRaw('COUNT(*) AS total')
			->whereIn('context_id', $journalIds)
			->where('status', self::STATUS_PUBLISHED)
			->groupBy('context_id')
			->get();
		return SiteStats::summarize($issues, $articles, $journalIds);
	}

	/**
	 * Whether Tricore or a child theme of Tricore is the active theme. Hooks stay
	 * registered after init() ran for the settings form, so they check this.
	 *
	 * @return bool
	 */
	protected function isThemeInChain() {
		$templateMgr = TemplateManager::getManager(Application::get()->getRequest());
		$theme = $templateMgr->getTemplateVars('activeTheme');
		while ($theme) {
			if ($theme === $this) {
				return true;
			}
			$theme = isset($theme->parent) ? $theme->parent : null;
		}
		return false;
	}
}
