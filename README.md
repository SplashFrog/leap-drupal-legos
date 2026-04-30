# Splash Frog - LEAP Legos

**Enterprise-grade, decoupled component building blocks for Drupal 11 Layout Builder.**

The LEAP Legos ecosystem provides a modern, modular approach to site building. By leveraging **Drupal 11 Recipes** for configuration and **Single Directory Components (SDCs)** for markup, these modules provide instant, battle-tested UI components without polluting your active configuration or locking you into rigid, legacy templating structures.

---

## ✨ Architectural Philosophy

Legacy Drupal modules often bundle configuration (via `config/install`) and rigid `.html.twig` templates directly into the module. This creates massive headaches for site builders when updates occur or when they want to customize the frontend.

**The LEAP Approach:**
1. **Decoupled Configuration (Recipes):** We do not force configuration on install. You enable the bridging module, and then manually apply the accompanying Drupal Recipe (`drush recipe ...`) to stamp the fields and block types into your active database. You own the config.
2. **Decoupled Markup (SDCs):** The `.module` files act *only* as data mappers. They extract raw data from Block Content or Paragraph entities, sanitize it, and pass clean variable arrays to your Theme's SDCs.
3. **Framework Agnostic (Almost):** While optimized for Bootstrap 5 via SDCs, the bridging logic maps data cleanly enough to be consumed by any frontend framework.

---

## 🛠️ Requirements

- **Drupal:** ^11.3
- **PHP:** >=8.3
- **Ecosystem:** `layout_builder`, `paragraphs`, `views`

---

## 🚀 Installation & Setup

Because this is an ecosystem of sub-modules, you only enable what you need.

**Step 1: Enable the bridging module**
```bash
drush en leap_lego_accordions
```

**Step 2: Apply the Configuration Recipe**
Every LEAP Lego module requires its corresponding recipe to be applied to generate the fields and block types. The module will display a warning messenger after enablement with the exact command to run.
```bash
drush recipe modules/contrib/leap-legos/modules/leap_lego_accordions/recipes/leap_lego_accordions
```

---

## 🧩 The Lego Modules

### Accordions (`leap_lego_accordions`)
Provides a Block Type that references "Accordion Item" Paragraphs. The module's preprocess logic extracts the parent block ID to guarantee that HTML `id` attributes required for Bootstrap collapse logic remain globally unique on the page.

### Cards (`leap_lego_cards`)
Provides a Block Type for rendering distinct content cards. The preprocess logic handles complex UI toggles, such as evaluating the `field_card_link_wrap` boolean to determine if the entire card DOM should be wrapped in an anchor tag, or just render a standalone CTA button.

### Image Carousel (`leap_lego_image_carousel`)
Provides a Block Type referencing paragraph slides. The module programmatically renders the attached Media (Image) entities through Drupal's Entity View Builder *before* passing them to the SDC, ensuring responsive image styles (`<picture>` tags) are perfectly calculated.

### Tabs (`leap_lego_tabs`)
Provides a Block Type referencing "Tab Item" Paragraphs. Similar to Accordions, it ensures deep DOM ID uniqueness so that Bootstrap 5 active state targeting functions flawlessly even when multiple tab blocks exist on the same page.

### Video Carousel (`leap_lego_video_carousel`)
Unlike the Image Carousel, this module directly references Media (Video) entities. The preprocess logic forces the rendering of oEmbed (YouTube/Vimeo) logic or native HTML5 `<video>` tags so the frontend SDC simply receives the final iframe/video DOM.

### Simple Navigation (`leap_lego_simple_nav`)
Provides a Block Type for rendering distinct lists of links. It flattens multi-value link fields into a clean, iterable array (`url` and `title`) for effortless Twig consumption.

### Sibling / Smart Navigation (`leap_lego_sibling_nav`)
The most complex of the Legos. It leverages embedded Views to build dynamic navigation trees.
- **Auto Navigation:** Inspects the active route, finds the current node, checks its `field_parent_content`, and injects that target ID into a View to automatically display all sibling pages.
- **Security:** Includes `_leap_lego_sibling_nav_parent_page_published()` to guarantee that unpublished or "archived" parent pages never expose their children in the navigation.

---

## 👨‍💻 Developer Information

### The Preprocess Pattern
All LEAP Legos follow a strict preprocess mapping pattern in their `.module` files:
1. Extract the `#block_content` entity.
2. Initialize default scalar variables.
3. Validate field existence (`$block->hasField(...)`).
4. Sanitize and extract data (e.g., `check_markup()` on rich text).
5. Inject a clean array (e.g., `accordion_items`) into the `$variables` array.

You will never find raw database queries or hardcoded HTML wrappers inside a LEAP Lego `.module` file.

---

## 🛡️ License
This module is part of the Splash Frog Ecosystem, the Drupal Ecosystem, and is provided under the GPL-2.0-or-later License.
