#!/usr/bin/env python3
"""One-off content/layout revisions applied to sections/*.json (idempotent).

- 1350px site width: desktop side padding of every section root becomes max(40px, (100% - 1350px) / 2).
- Removes Jason Dsouza signatures/contact card, office hours (incl. the Call-us card note), Find-us rings.
- Products: catalogue buttons removed.
- Brands: working category filter (ids for theme.js); brand cards say "Enquire" and link to Contact (no catalogue downloads).
- Contact: static form fields replaced by a slot the theme fills with the CF7 quotation form.
"""
import glob, json, os, re, xml.etree.ElementTree as ET

HERE = os.path.dirname(os.path.abspath(__file__))
SEC = os.path.join(HERE, "..", "sections")
GUTTER = "max(40px, calc((100% - 1350px) / 2))"
CONTACT = "https://soharon.co.uk/onora/contact-us/"
BRAND_FILTERS = ["all", "tableware", "cutlery", "glass-drinkware", "buffet-service", "kitchen", "lighting", "indian-tableware"]
REMOVE = {
    "home-02-intro.json": ["Intro signature"],
    "about-02-our-story.json": ["Signature"],
    "contact-03-enquiry.json": ["Contact person", "Hours", "Form row 1", "Form row 2", "Interests", "Form row 3", "Submit row"],
    "contact-04-map.json": ["Map ring large", "Map ring small"],
    **{f"products-{n:02d}-{slug}.json": [f"C{n-2:02d} catalogue"] for n, slug in [
        (3, "chinaware"), (4, "cutlery"), (5, "glassware"), (6, "polycarbonate-drinkware"), (7, "melamine"), (8, "kitchen"),
        (9, "buffet"), (10, "bar-items"), (11, "foh-trolleys"), (12, "accessories"), (13, "table-lamps"), (14, "indian-tableware")]},
}


def remove(d, cids):
    root = ET.fromstring("<root>" + d["xml_structure"] + "</root>")
    parent = {c: p for p in root.iter() for c in p}
    gone = set()
    for el in list(root.iter()):
        if el.get("configuration-id") in cids:
            gone |= {x.get("configuration-id") for x in el.iter()}
            parent[el].remove(el)
    d["xml_structure"] = "".join(ET.tostring(c, encoding="unicode", short_empty_elements=False) for c in root)
    for m in ("element_config", "style", "classes", "interactions"):
        for k in gone:
            d.get(m, {}).pop(k, None)


def widen(d):
    root_id = re.match(r'<[\w-]+ configuration-id="([^"]+)"', d["xml_structure"]).group(1)
    st = d.get("style", {}).get(root_id, "")
    head, sep, tail = st.partition("@media")  # only the desktop declarations
    head = re.sub(r"padding: (\S+) 100px (\S+) 100px;", rf"padding: \1 {GUTTER} \2 {GUTTER};", head)
    head = re.sub(r"padding: (\S+) 100px;", rf"padding: \1 {GUTTER};", head)
    d["style"][root_id] = head + sep + tail


for f in sorted(glob.glob(os.path.join(SEC, "*.json"))):
    name = os.path.basename(f)
    d = json.load(open(f))
    if name in REMOVE:
        remove(d, set(REMOVE[name]))
    if name == "contact-02-quick-contact.json":  # no office hours on the site
        d["element_config"]["Card Call us note"]["paragraph"] = "Call or WhatsApp us"
    if name == "brands-02-brand-partners.json":
        for k, v in d["element_config"].items():
            if re.fullmatch(r"Brand \d+ foot", k) and "link" in v:
                v["link"]["destination"] = CONTACT
            if re.fullmatch(r"Brand \d+ cta", k):
                v["paragraph"] = "Enquire"
            if isinstance(v.get("paragraph"), str):  # former catalogue cards used a download arrow
                v["paragraph"] = v["paragraph"].replace("↓", "→")
    if name == "contact-03-enquiry.json" and "Quote form slot" not in d["xml_structure"]:
        # the theme injects the Contact Form 7 quotation form into this element (id onora-quote-form)
        d["xml_structure"] = d["xml_structure"].replace(
            '<e-paragraph configuration-id="Form note"></e-paragraph>',
            '<e-paragraph configuration-id="Form note"></e-paragraph><e-div-block configuration-id="Quote form slot"></e-div-block>')
        d["element_config"]["Quote form slot"] = {"_cssid": "onora-quote-form"}
        d["style"]["Quote form slot"] = "padding: 0px; width: 100%; min-width: 0px;"
    if name == "brands-02-brand-partners.json":  # working brand filter (behaviour in theme.js)
        cfg, st = d["element_config"], d["style"]
        cfg.setdefault("Filter chips", {})["_cssid"] = "brand-filters"
        cfg.setdefault("Brand grid", {})["_cssid"] = "brand-grid"
        for i, slug in enumerate(BRAND_FILTERS, 1):
            cfg[f"Chip {i}"]["link"] = {"destination": "#" + slug, "isTargetBlank": False, "tag": "a"}
        st["Chip 1"] = st["Chip 2"]  # active state comes from the theme (.is-active)
        for i in range(1, 20):
            name_ = re.sub(r"&amp;|&", "and", cfg[f"Brand {i} name"]["paragraph"])
            cfg.setdefault(f"Brand {i}", {})["_cssid"] = "brand-" + re.sub(r"[^a-z0-9]+", "-", name_.lower().replace("ö", "o")).strip("-")
    if name == "home-02-intro.json":  # keep the "12 curated collections" badge inside the image composition
        d["style"]["Intro badge"] = d["style"]["Intro badge"].replace("left: 470px; top: 520px;", "left: auto; right: 0px; top: 520px;")
    widen(d)
    json.dump(d, open(f, "w"), indent=1, ensure_ascii=False)
