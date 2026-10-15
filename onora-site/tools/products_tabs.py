#!/usr/bin/env python3
"""Products page secondary header: one-line tab bar (All + 12 collections).

Writes sections/products-02-tabs.json. Behaviour (sticky, active state, filtering the
collection sections) lives in the ONORA theme: theme/onora/assets/js/theme.js + theme.css.
"""
import json, os

BASE = "https://soharon.co.uk/onora/products/"
TABS = [("All", "all"), ("Chinaware", "chinaware"), ("Cutlery", "cutlery"), ("Glassware", "glassware"),
        ("Polycarbonate Drinkware", "polycarbonate-drinkware"), ("Melamine", "melamine"), ("Kitchen", "kitchen"),
        ("Buffet", "buffet"), ("Bar Items", "bar-items"), ("FOH Trolleys", "foh-trolleys"), ("Accessories", "accessories"),
        ("Table Lamps", "table-lamps"), ("Indian Tableware", "indian-tableware")]

x = ['<e-flexbox configuration-id="Product tabs"><e-flexbox configuration-id="Product tabs track">']
cfg = {"Product tabs": {"tag": "nav", "_cssid": "product-tabs"}, "Product tabs track": {"_cssid": "product-tabs-track"}}
st = {
    "Product tabs": "padding: 0px max(40px, calc((100% - 1350px) / 2)); background: #FFFFFF; border-width: 0px 0px 1px 0px; border-style: solid; border-color: #E6E0D6;"
                    " @media(--tablet){ padding: 0px 40px; } @media(--mobile){ padding: 0px 20px; }",
    "Product tabs track": "padding: 0px; gap: 8px; justify-content: space-between; align-items: stretch; @media(--tablet){ gap: 28px; } @media(--mobile){ gap: 22px; }",
}
for label, slug in TABS:
    k = f"Tab {label}"
    x.append(f'<e-paragraph configuration-id="{k}"></e-paragraph>')
    cfg[k] = {"paragraph": label, "link": {"destination": BASE + "#" + slug, "isTargetBlank": False, "tag": "a"}}
    st[k] = ("font-family: var(--on-sans); font-weight: 600; font-size: 11px; letter-spacing: 1.2px; text-transform: uppercase; line-height: 1.2;"
             " color: #16213A; padding: 22px 0px 20px 0px; border-width: 0px 0px 2px 0px; border-style: solid; border-color: transparent;"
             " transition: color 0.3s; &:hover{ color: #C9962E; } @media(--mobile){ padding: 18px 0px 16px 0px; }")
x.append("</e-flexbox></e-flexbox>")
out = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "sections", "products-02-tabs.json")
json.dump({"post_id": 28, "xml_structure": "".join(x), "element_config": cfg, "style": st}, open(out, "w"), indent=1, ensure_ascii=False)
print(out)
