# User Guide

For Journal Managers and Site Administrators who set up the look of a journal with the Tricore theme. All options are under **Settings > Website > Appearance > Theme** (journal) or **Administration > Site Settings > Appearance > Theme** (site), once Tricore is selected as the theme.

## Where things are set

Tricore uses the built-in OJS settings for everything OJS already provides, and adds its own options for the design.

| Set in the built-in OJS settings | Menu |
|----------------------------------|------|
| Logo, favicon, homepage image | Website > Appearance > Setup |
| Page footer text (address, ISSN, license) | Website > Appearance > Setup > Page Footer |
| Navigation menus and custom links | Website > Setup > Navigation (`primary` and `user` areas) |
| Sidebar blocks (Information, Language, etc.) | Website > Appearance > Setup > Sidebar |
| Announcements on the homepage | Website > Setup > Announcements |
| Static pages | Website > Static Pages (3.3/3.4) or Navigation (3.5) |

| Set in the Tricore theme options | Group |
|----------------------------------|-------|
| Colours | Colours |
| Font | Typography |
| Header layout, page width, corner rounding | Layout |
| Sidebar per page type | Sidebar |
| Hero, buttons, summary, current issue, latest articles, metrics, indexing | Homepage content |
| Views and downloads | Article statistics |
| Social media links | Social media |

## Colours

| Option | Default | Used for |
|--------|---------|----------|
| Primary colour | `#1f5fbf` | Links, primary buttons, icons, highlights, keyboard focus |
| Secondary colour | `#0f2a44` | Hero section of the journal homepage |
| Accent colour | `#e0a526` | Section title underlines, metric value lines, badge dots |
| Header background | `#ffffff` | Header and main navigation |
| Footer background | `#0f2a44` | Footer |

- Pick a colour with the colour picker or type a hex code (`#rrggbb` or `#rgb`).
- The text colour on each background is chosen automatically (black or white, whichever has more contrast). Every valid hex colour is accepted.
- For the best readability, choose colours that are clearly dark or clearly light. Mid-tones (for example grey `#777777`) are accepted, but give lower text contrast.
- The site landing page derives its background (paper tone, grid and glows) from the primary colour, so the site's primary colour changes its overall mood.

## Typography

| Choice | Headings | Body |
|--------|----------|------|
| System font (default) | Device font | Device font |
| Noto Sans | Noto Sans | Noto Sans |
| Noto Serif headings with Noto Sans body | Noto Serif | Noto Sans |
| Lora headings with Open Sans body | Lora | Open Sans |
| Lato | Lato | Lato |

All fonts are served from your own journal server (the fonts bundled with OJS). No external service such as Google Fonts is used. The system font is the fastest, because nothing is downloaded.

## Layout

| Option | Choices | Notes |
|--------|---------|-------|
| Header layout | Logo on the left, menu on the right (default); Logo and menu centred | On small screens both become a logo with a Menu button |
| Page width | Narrow 1080px, Normal 1200px (default), Wide 1360px | Maximum width of the content |
| Corner rounding | None 0, Small 4px, Medium 8px (default), Large 16px | Cards, buttons, images and input fields |

## Sidebar

The option **Show the sidebar on** has one checkbox per page type. Each checkbox works on its own:

| Checkbox | Pages |
|----------|-------|
| Journal homepage | `/index.php/<journal>` |
| Archive | The list of issues |
| Issue (table of contents) | A single issue and the current issue |
| Article page | The article detail page |
| About pages | About, Editorial Team/Masthead, Submissions, Contact and other pages under `/about` |
| Static pages | Static Pages and custom menu pages |
| Search | The search page and its results |
| Announcements | The announcement list and detail pages |

- All boxes are ticked by default, so the behaviour matches standard OJS.
- The sidebar is only shown when blocks are selected under Appearance > Setup > Sidebar.
- Login, registration, lost/reset password and the site landing page never show the sidebar.
- Other pages (for example the information pages for readers) follow the standard OJS behaviour.

## Journal homepage content

The journal homepage is built from top to bottom:

1. **Hero:** large title, subtitle, two buttons and the homepage image (if any).
2. **Journal metrics**
3. **Journal summary**
4. **Announcements** (built-in OJS setting)
5. **Latest articles**
6. **Current issue**
7. **Indexing information**
8. **Additional homepage content** (built-in OJS setting)

| Option | Notes |
|--------|-------|
| Hero section | Shows the hero. When turned off, the homepage image is shown as in the default theme. |
| Hero title* | Empty = journal name. |
| Hero subtitle* | Empty = journal summary (shortened to about 260 characters). Line breaks are kept. |
| Primary button label / link | Empty = "Current Issue", linking to the current issue. |
| Secondary button label / link | Empty = "Submissions", linking to the submission guidelines. |
| Journal summary | Shows the Journal Summary as its own section. |
| Current issue | Shows the table of contents of the current issue. |
| Number of latest articles | 0, 3, 6 (default) or 9 article cards from all issues. 0 hides the section. |
| Journal metrics* | One metric per line as `Label\|Value`, up to 8 lines. Examples: `Acceptance rate\|25%`, `Review time\|30 days`, `Impact factor\|1.2`. |
| Indexing information* | Rich text (bold, italic, links) listing the databases and indexes that include the journal. Empty = section hidden. |

\* Multilingual option. Fill it in per language using the language tabs above the field. When a language is empty, the journal's primary language is used.

**Button links** accept a full `https://...` address or a path on your site (`/index.php/journal/about`, `about/submissions`, `#homepageIssue`). Unsafe links such as `javascript:` are rejected.

## Article statistics

The option **Views and downloads** (on by default) shows the number of views and downloads:

- on every article in an issue's table of contents, in search results and in the current issue on the homepage;
- on the latest article cards;
- on the archive cards, as the total for all articles in that issue;
- in the details column of the article page, in two boxes "Views" and "Downloads".

The definitions are the same as in the OJS statistics:

- **Views** are views of the abstract page.
- **Downloads** are downloads of galley files (PDF, HTML) and supplementary files.

Large numbers are shortened (1.2K, 3.4M) and the full number appears on hover. The numbers update when OJS processes its statistics: in 3.3 through the Usage Statistics plugin and its scheduled task, in 3.4/3.5 through the statistics job queue.

## Social media

Enter the full `https://...` address of the journal's Facebook, X (Twitter), Instagram, LinkedIn and YouTube profiles. Empty fields are not shown. The icons appear in the footer under "Follow us" and open in a new tab.

## Site landing page

When Tricore is selected as the site theme, the site homepage becomes a landing page:

1. **Hero:** a badge with journal covers and the number of journals and articles, the site title, and the site About text (Administration > Site Settings > Information).
2. **Journal cards:**
   - each card shows the acronym, the number of issues and articles, a small cover, the name, the summary, a View Journal button, a Current Issue link and the online/print ISSN;
   - the second card is highlighted;
   - only journals that are enabled and listed on the site are included.
3. **Article search** across all journals, with the number of journals, issues and articles.
4. **Sign-up call to action:** shown to visitors who are not logged in.

Covers come from each journal's **Journal Thumbnail** (Settings > Journal > Appearance). ISSNs come from the journal's ISSN settings. The landing background follows the site's primary colour.

## Other pages

- **Login, registration, lost/reset password:** a centred card with labels above the fields and a full-width primary button. Registration uses two columns on wide screens.
- **Archive:** a grid of issue cards with covers (from each issue's Cover Image) and total views/downloads. On phones the cards are horizontal.
- **Article page:** a full-width two-column layout, with the statistics in the details column.
- **Footer:**
  - top: the Page Footer text and social media links;
  - bottom: journal name, theme credit and the OJS/PKP logo.

## Accessibility

- Keyboard focus is always visible and the "skip to content" link is kept.
- The mobile menu and submenus use `aria-expanded`, and the Escape key closes them.
- Text colours are chosen automatically for high contrast.
- Animations (landing page, cards) stop when the visitor has enabled "reduce motion" in their operating system.
- The layout uses logical CSS properties, so right-to-left languages display correctly.

## Tips

- After changing options, OJS rebuilds the CSS automatically. Reload the page with Ctrl+F5 if the browser still shows the old version.
- If the Appearance form refuses to save, look for the red message below a field. The usual causes are an invalid colour code or a social media link without `https://`.
- Saved options always take precedence over default values. If you use a child theme that changes the defaults, clear or reset an option to see the child theme's default.
