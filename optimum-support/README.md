# Optimum Support – Freshdesk portal theme (Figma redesign)

`optimum-portal-theme/` is the full replacement theme. It has the same folders and file names as the Freshdesk theme editor. Paste each file into the matching file in the editor.

Design source: Figma "OPTIMUM-SUPPORT", page "Support Portal – Desktop".

## Install order
1. **Layout → header.html** and **Layout → footer.html.** The shared theme styles are split between a `<style>` block at the top of each file. Each file must stay under about **50,000 characters**: the Freshdesk editor silently didn't save a 50 KB header, while 43 KB saved fine. Both are now about 36 KB. The styles live here, not in `head.html` or the Stylesheet, because these files are printed after Freshdesk's own `theme.css`, so our rules win.
2. **Layout → head.html** (adds the Poppins and Cairo fonts).
3. **Layout → layout.html** (adds the "Can’t find what you’re looking for?" band on Home and Knowledge base pages).
4. (footer.html also holds the new footer and, at the end, the guided ticket-form script, logic unchanged.)
5. **The changed pages:** `portal-home`, `login-page`, `new-user-signup`, `submit-ticket`, `ticket-list`, `article-view`, `folder-view`, `solutions-home`.
6. **Stylesheet → css.css:** leave it empty (it only holds a note).
7. Every other page is unchanged and picks up the new look from the styles in `header.html`.

**Check after pasting:** the source of `/support/home` should contain `.opt-hero` and should no longer contain `/* Optimum: hover effect for home cards`. If it still does, the home page has not been replaced yet.

## Things to set
- **Logo:** the header and footer both use the portal logo from *Admin → Portals → Logo*. The footer and login panel show it in white automatically, so the 25 KB embedded logo is no longer needed.
- **Social icons (optional):** at the top of `footer.html`, paste the X, Instagram or LinkedIn URLs into `opt_x_url`, `opt_instagram_url` and `opt_linkedin_url`. Any left empty stay hidden.

## What was kept (functions checklist)
- Guided "Submit a ticket" form. It has the 6 topics, the closest-match options, and the hidden Category and Sub-category values. The ID or passport field shows only for the 7 listed organisations. "General question" has its own mode, the request title fills in automatically, and the form checks required fields itself. If anything fails, it falls back to Freshdesk's normal form. The only changes are one extra line of text and an icon wrapper; this was tested in a browser.
- Home: search, home cards, 3 knowledge-base categories, forum categories, popular articles, trending topics, card hover effects, and page messages.
- Article: "Modified on" date without the author, print button, other articles in the folder, similar articles, and the "Was this helpful?" feedback.
- Login: error messages, forgot password (same `#loginForm`, `#forgetPassForm` and `#forgetPassLink` IDs), social login, agent login and customer login. Sign-up: the sign-up form and agent login.
- Search results, tickets (list, filters, export, pagination, view, reply, survey, add people, close, CC), archived and public tickets, and all Discussions pages. Their Liquid code is unchanged.
- Header tabs, user menu, language list, skip-to-content link, the survey (CSAT) header, footer branding, cookie and privacy notices, and cookie settings.

## Added from Figma
- Dark hero with "Popular:" links (filled from the 3 most popular articles).
- Action cards with call-to-action links.
- Category cards that show their folder and articles.
- "Can’t find what you’re looking for?" band.
- New footer.
- Page headers with subtitles.
- Submit-ticket sidebar: "Looking for a quick answer?" and "Before you submit".
- Two-panel login and sign-up pages.
- "Browse categories" sidebar on folder pages.
- "Still have questions?" box on articles.
- Mobile layout for all of the above.
