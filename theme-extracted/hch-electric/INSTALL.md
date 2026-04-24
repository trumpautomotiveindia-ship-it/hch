# HCH Electric — Installation & Setup

## 1. Install the theme

**Option A — ZIP upload (recommended)**
1. Download `hch-electric.zip`.
2. In your WordPress admin → **Appearance → Themes → Add New → Upload Theme**.
3. Choose the zip, click **Install Now**, then **Activate**.

**Option B — FTP / SSH**
1. Unzip the file.
2. Upload the `hch-electric/` folder into `wp-content/themes/`.
3. Activate under **Appearance → Themes**.

## 2. Install WooCommerce
The theme requires **WooCommerce 7.0+**. Install it under **Plugins → Add New → WooCommerce → Install Now**.

## 3. Create product data

### ⚡ Fastest option — one-click Demo Data Seeder
The theme includes a built-in seeder that populates everything for you.

1. After activating WooCommerce, go to **Tools → HCH Demo Data** in wp-admin.
2. Click **Seed Demo Data**.
3. In ~5 seconds you'll have: the `Brand` attribute + 10 brand terms, 16 product categories with emoji icons, and 54 sample products with prices, specs, MOQ and stock badges.

The seeder is **idempotent** — rerunning it skips anything that already exists. Safe to use on staging or after partial imports. Skip sections below if you used the seeder.

### Manual setup (if you prefer)

### Product categories (for the category bar)
Go to **Products → Categories** and add terms such as:

| Name | Slug | Icon (emoji) |
|---|---|---|
| Battery & BMS | `battery` | 🔋 |
| Chargers | `charger` | ⚡ |
| Controllers | `controller` | ⚙️ |
| Motors | `motor` | 🔩 |
| Throttles | `throttle` | 🎛️ |
| Cables | `cable` | 🔌 |
| DC-DC | `dcdc` | 🔄 |
| Brakes | `brake` | 🛑 |
| Body Kits | `body` | 🏍️ |
| Suspension | `suspension` | 🔧 |
| Switches | `switch` | 💡 |
| E-Rickshaw | `erickshaw` | 🛺 |
| E-Cycle | `ecycle` | 🚲 |
| Conversion | `conversion` | 🧰 |
| Testing | `testing` | 🧪 |
| Clearance | `clearance` | 🏷️ |

The **HCH icon (emoji)** field appears when you edit each category term.

### Brand attribute (for the brand filter chips)
1. **Products → Attributes → Add new attribute**: Name `Brand`, slug `brand`, enable archives.
2. Under **Configure terms**, add: OLA S1, Ather 450, Bajaj Chetak, TVS iQube, Hero Vida, Ampere, Okinawa, Revolt, E-Rickshaw, Local EVs.
3. On each product, assign the Brand attribute terms under **Product data → Attributes**.

### Products
**Products → Add New** — use the regular WooCommerce fields, plus the **HCH Electric — Product Details** meta box (right sidebar) for:
- Spec line (e.g. `67.2V · 6A · NMC`)
- MOQ (minimum order quantity; also enforced on add-to-cart)
- Badge (`IN STOCK`, `POPULAR`, `NEW`, `DEAL`)
- Fallback emoji icon (shown if no featured image)
- Price note (default `/pc excl. GST`)

## 4. Set up the homepage
- **Settings → Reading → Your homepage displays → A static page** → pick any published page.
- The theme automatically uses `front-page.php` whichever page is selected.
- Edit hero copy, ticker and stats under **Appearance → Customize → HCH Electric**.

## 5. Configure WhatsApp checkout
- **Appearance → Customize → HCH Electric → Contact & WhatsApp** → enter your WhatsApp number (country code first, no +, e.g. `919876543210`).
- The "Order via WhatsApp" button in the cart drawer will pre-fill the entire cart as a message.

## 6. Block Editor patterns
When editing any page or post, open the **Patterns** panel and look under the **HCH Electric** category. 14 patterns are available:

**Homepage sections**
- HCH Ticker — scrolling announcement bar
- HCH Hero — kicker + bold headline + stats row
- HCH Brand Filter — brand attribute chips (native block)
- HCH Category Bar — sticky category navigation (native block)
- HCH Product Grid — 6-column WooCommerce grid
- HCH Full Homepage — all-in-one one-click rebuild

**Marketing sections**
- HCH Feature Grid — 3-column benefit cards
- HCH CTA Banner — dark conversion banner with WhatsApp + shop buttons
- HCH Testimonials — 3-column quote cards
- HCH Trust Bar — supported-models strip
- HCH Contact Card — WhatsApp + email + address tiles
- HCH FAQ — expandable native FAQ list
- HCH Newsletter CTA — signup row
- HCH Category Pills — visual pill chips

## 6a. Native Gutenberg Blocks
Two blocks are also registered as first-class blocks (not just patterns). Search for them in the inserter under the **HCH Electric** block category:

- **HCH Brand Filter** (`hch/brand-filter`) — renders the WooCommerce `pa_brand` chips with a live editor preview.
- **HCH Category Bar** (`hch/category-bar`) — renders the `product_cat` sticky nav with a live editor preview.

Both blocks support the standard WordPress controls (Wide / Full alignment, margin spacing) in the right sidebar. The legacy shortcodes `[hch_brand_filter]` and `[hch_category_bar]` continue to work for backward compatibility.

## 7. Custom Block Styles
When you select any compatible block (Group, Button, Heading, Paragraph, Columns, Separator, List, Quote, Cover), the Styles tab in the right sidebar now includes HCH-branded variations:

| Block | HCH Variations |
|---|---|
| Group | HCH Dark Card · HCH Glass · HCH Bordered |
| Button | HCH Cyan · HCH Green · HCH Ghost · HCH WhatsApp |
| Heading | HCH Mono Kicker · HCH Big Display |
| Paragraph | HCH Mono |
| Columns | HCH Stats Grid |
| Separator | HCH Glow Line |
| List | HCH Checklist |
| Quote | HCH Testimonial |
| Cover | HCH Hero |

## 8. Page Templates
Two extra templates are available from the Page Attributes → Template dropdown:

- **Full Width** — keeps header + footer but removes the default container.
- **Canvas (No Header/Footer)** — blank full-bleed canvas, ideal for landing pages, promotions and email-campaign destinations.

## 9. Rebuild the homepage in the Block Editor
Once the theme is active:
1. Create a new Page called **Home** (or reuse an existing one).
2. Click the **⊕ Patterns** button in the editor sidebar → **HCH Electric** category → click **HCH Full Homepage** (or mix-and-match any patterns).
3. Edit text, reorder blocks, or add any native block.
4. Publish.
5. **Settings → Reading → A static page → Home**.

The theme will automatically render your block content on the front page. If the selected page is empty, the built-in curated layout (hero + brand filter + category bar + product grid) is used as a fallback.

## 10. Menus
- **Appearance → Menus** → create a menu and assign to **Footer — Categories** or **Footer — Contact** locations.
- If left unassigned, the footer falls back to the first 6 product categories and a built-in contact list.

## Troubleshooting
- **No products show on homepage**: Make sure WooCommerce is active and at least one product is published.
- **Brand filter is empty**: Create the `Brand` product attribute (slug `brand`) and assign terms to products.
- **Cart drawer shows "Your cart is empty" after adding**: Ensure your site is accessible via `admin-ajax.php` (not blocked by a caching plugin / firewall).
- **WhatsApp button does nothing**: Make sure the number in the Customizer is numeric only (no `+`, no spaces).

## Support
For theme questions, email `hchevinternational@gmail.com`.
