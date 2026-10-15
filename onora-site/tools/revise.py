#!/usr/bin/env python3
"""One-off content/layout revisions applied to sections/*.json (idempotent).

- 1350px site width: desktop side padding of every section root becomes max(40px, (100% - 1350px) / 2).
- Removes Jason Dsouza signatures/contact card, office hours (incl. the Call-us card note), Find-us rings.
- Brands: brand cards say "Enquire" and link to Contact (no catalogue downloads).
"""
import glob, json, os, re, xml.etree.ElementTree as ET

HERE = os.path.dirname(os.path.abspath(__file__))
SEC = os.path.join(HERE, "..", "sections")
GUTTER = "max(40px, calc((100% - 1350px) / 2))"
CONTACT = "https://soharon.co.uk/onora/contact-us/"
REMOVE = {
    "home-02-intro.json": ["Intro signature"],
    "about-02-our-story.json": ["Signature"],
    "contact-03-enquiry.json": ["Contact person", "Hours"],
    "contact-04-map.json": ["Map ring large", "Map ring small"],
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
    widen(d)
    json.dump(d, open(f, "w"), indent=1, ensure_ascii=False)
