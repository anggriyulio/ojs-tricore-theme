# Installation

How to install, enable, update and remove the Tricore theme on OJS 3.3, 3.4 and 3.5.

## Requirements

| OJS | Branch | Release package | PHP | Tested on |
|-----|--------|-----------------|-----|-----------|
| 3.3.x | `stable-3_3_0` | `tricore-<version>-ojs3.3.tar.gz` | 7.3 or later | 3.3.0.23 |
| 3.4.x | `stable-3_4_0` | `tricore-<version>-ojs3.4.tar.gz` | 8.0.2 or later | 3.4.0.11 |
| 3.5.x | `stable-3_5_0` (also `main`) | `tricore-<version>-ojs3.5.tar.gz` | 8.2 or later | 3.5.0.5 |

- Use the package or branch that matches your OJS version. Plugin classes, locale folders and some templates differ between OJS versions, so a package for another version will not work.
- The default OJS theme (`plugins/themes/default`) must stay installed. Tricore uses its stylesheet and fonts as a base layer. It does not need to be enabled.

## Installing

### From a release package (recommended)

1. Download the package for your OJS version from the [Releases](https://github.com/anggriyulio/ojs-tricore-theme/releases) page.
2. Install it in one of two ways:
   - **Upload:** go to **Settings > Website > Plugins > Upload A New Plugin** and upload the `.tar.gz` file.
   - **Command line:** extract the package so that the files end up in `plugins/themes/tricore` (the folder name **must** be `tricore`), then run from the OJS root:
     ```bash
     php lib/pkp/tools/installPluginVersion.php plugins/themes/tricore/version.xml
     ```
3. Clear the cache so templates and CSS are rebuilt: delete the contents of `cache/t_compile`, the `cache/*.css` files and the `cache/fc-*` files.

### From git

Clone the branch for your OJS version into `plugins/themes/tricore`, for example for OJS 3.4:

```bash
git clone -b stable-3_4_0 https://github.com/anggriyulio/ojs-tricore-theme.git plugins/themes/tricore
php lib/pkp/tools/installPluginVersion.php plugins/themes/tricore/version.xml
```

The `screenshots/` and `.github/` folders are only used on GitHub and can be deleted.

## Enabling

### For a journal

1. Log in as Journal Manager.
2. Go to **Settings > Website > Plugins**, find **Tricore Theme (3Core)** and enable it.
3. Go to **Settings > Website > Appearance > Theme**, select **Tricore Theme (3Core)** and save.
4. The theme options appear below the theme selector. Every option has a default value, so the theme works without any configuration. See the [user guide](user-guide.md).

### For the site (landing page)

1. Log in as Site Administrator.
2. Go to **Administration > Site Settings > Plugins** and enable Tricore at site level.
3. Go to **Administration > Site Settings > Appearance > Theme**, select Tricore and save.
4. The site homepage (`/index.php/index`) now shows the Tricore landing page: journal cards, article search across all journals and a sign-up call to action.

Site options are stored separately from each journal's options. Colours, typography, layout and footer of the site are set under **Administration > Site Settings > Appearance**.

## Updating

1. Back up your database and the `plugins/themes/tricore` folder.
2. Install the new package the same way as above (upload, or replace the folder contents). Saved options are kept, because they are stored in the database.
3. If you replaced the files manually, register the new version with `installPluginVersion.php`.
4. Clear the cache.
5. Read the [changelog](../CHANGELOG.md) for changes that need attention.

## Uninstalling

1. Select another theme under **Appearance > Theme** for every journal (and the site) that uses Tricore.
2. Disable the plugin under **Website > Plugins**.
3. Delete the `plugins/themes/tricore` folder. Saved options remain in the `plugin_settings` table with `plugin_name = 'tricorethemeplugin'` and can be deleted manually if you will not use the theme again.

Child themes depend on Tricore. Disable any child theme before removing Tricore.

## Troubleshooting

| Symptom | Cause and solution |
|---------|--------------------|
| The design does not change after saving options | OJS clears the CSS cache when Appearance is saved. If nothing changes, delete `cache/*.css` and reload with Ctrl+F5. |
| Tricore is not listed under Appearance > Theme | The plugin is not enabled for this journal or the site (Website > Plugins), or its version was not registered. |
| Error 500 after copying the files | Check that the folder is named `tricore` and that the package matches your OJS version. Check the PHP or web server error log. |
| The sidebar does not appear | Choose sidebar blocks under Appearance > Setup > Sidebar and tick the page type under **Show the sidebar on**. Login, registration and the site landing page never show the sidebar. |
| Views and downloads show 0 | OJS has not processed usage statistics yet. The numbers come from the OJS usage statistics and grow after the scheduled statistics task has run. |
