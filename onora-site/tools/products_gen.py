#!/usr/bin/env python3
"""Generate the PRODUCTS page (post 28) section payloads -> onora-site/sections/products-NN-*.json"""
import json, os
HERE = os.path.dirname(os.path.abspath(__file__))
ROOT = "/home/user/alkhoory/onora-site"
exec(open(os.path.join(HERE, "products-data.py")).read())  # defines COLL
MEDIA = json.load(open(os.path.join(ROOT, "tools/media.json")))
POST = 28
BASE = "https://soharon.co.uk/onora/"
GOLD = "linear-gradient(90deg, #A5740B 0%, #DEAF57 25%, #F1C96A 50%, #BE8826 75%, #A5740B 100%)"
USE_IDS = os.environ.get("ANCHORS", "1") == "1"


def mid(name):
    return MEDIA[name]["id"]


def murl(name):
    return MEDIA[name]["url"]


def link(url, blank=False):
    return {"destination": url, "isTargetBlank": blank, "tag": "a"}


def save(nn, name, p):
    p["post_id"] = POST
    f = os.path.join(ROOT, "sections", f"products-{nn:02d}-{name}.json")
    json.dump(p, open(f, "w"), indent=1, ensure_ascii=False)
    print(f)


RING = "position: absolute; padding: 0px; border-radius: 50%; border-style: solid; z-index: 0; "


def hero():
    x, cfg, st, cl = [], {}, {}, {}
    x.append('<e-flexbox configuration-id="Products hero"><e-div-block configuration-id="Hero ring outer"></e-div-block><e-div-block configuration-id="Hero ring inner"></e-div-block>'
             '<e-flexbox configuration-id="Hero content"><e-paragraph configuration-id="Hero breadcrumb"></e-paragraph><e-heading configuration-id="Hero title"></e-heading>'
             '<e-div-block configuration-id="Hero rule"></e-div-block><e-paragraph configuration-id="Hero lead"></e-paragraph></e-flexbox></e-flexbox>')
    cfg["Products hero"] = {"tag": "section"}
    st["Products hero"] = ("min-height: 560px; padding: 74px 100px 60px 100px; position: relative; overflow: hidden; flex-direction: column; align-items: flex-start; background-color: #071F44;"
                           f" background-image: linear-gradient(90deg, rgba(7,31,68,0.96) 0%, rgba(11,47,98,0.78) 55%, rgba(11,47,98,0.3) 100%), url({murl('products-hero-bg.jpg')});"
                           " background-size: cover; background-position: center center; background-repeat: no-repeat;"
                           " @media(--tablet){ padding: 72px 40px 64px 40px; min-height: 480px; } @media(--mobile){ padding: 56px 20px 56px 20px; min-height: 0px; }")
    st["Hero ring outer"] = RING + "top: -120px; left: 980px; width: 620px; height: 620px; min-width: 620px; border-width: 1.2px; border-color: rgba(201,150,46,0.45); @media(--tablet){ left: auto; right: -260px; } @media(--mobile){ display: none; }"
    st["Hero ring inner"] = RING + "top: -45px; left: 1055px; width: 470px; height: 470px; min-width: 470px; border-width: 1px; border-color: rgba(201,150,46,0.25); @media(--tablet){ left: auto; right: -185px; } @media(--mobile){ display: none; }"
    st["Hero content"] = "flex-direction: column; gap: 24px; padding: 0px; width: 780px; max-width: 100%; align-items: flex-start; z-index: 1;"
    cfg["Hero breadcrumb"] = {"paragraph": "Home&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;Products"}
    st["Hero breadcrumb"] = "font-family: var(--on-sans); font-weight: 500; font-size: 12px; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,0.6); line-height: 1.2;"
    cfg["Hero title"] = {"title": "Twelve collections for every moment of service.", "tag": "h1"}
    st["Hero title"] = "font-family: var(--on-serif); font-weight: 600; letter-spacing: -0.3px; font-size: 88px; line-height: 88px; color: #FFFFFF; @media(--tablet){ font-size: 64px; line-height: 66px; } @media(--mobile){ font-size: 44px; line-height: 48px; }"
    st["Hero rule"] = f"width: 90px; min-width: 90px; height: 2px; padding: 0px; background-image: {GOLD};"
    cfg["Hero lead"] = {"paragraph": "From the first coffee of the morning to the last cocktail of the night — explore our complete hospitality range in one place."}
    st["Hero lead"] = "font-family: var(--on-sans); font-weight: 400; font-size: 17px; line-height: 30px; color: rgba(255,255,255,0.82); width: 560px; max-width: 100%; @media(--mobile){ font-size: 15px; line-height: 26px; }"
    return {"xml_structure": "".join(x), "element_config": cfg, "style": st, "classes": cl}


def index():
    x, cfg, st, cl = [], {}, {}, {}
    x.append('<e-flexbox configuration-id="Collection index"><e-flexbox configuration-id="Index head"><e-flexbox configuration-id="Index label row">'
             '<e-div-block configuration-id="Index rule"></e-div-block><e-paragraph configuration-id="Index label"></e-paragraph></e-flexbox>'
             '<e-paragraph configuration-id="Index meta"></e-paragraph></e-flexbox><e-flexbox configuration-id="Index chips">')
    cfg["Collection index"] = {"tag": "nav"}
    st["Collection index"] = ("flex-direction: column; gap: 28px; padding: 56px 100px; background: #FFFFFF; border-width: 0px 0px 1px 0px; border-style: solid; border-color: #E6E0D6;"
                              " @media(--tablet){ padding: 48px 40px; } @media(--mobile){ padding: 40px 20px; gap: 22px; }")
    st["Index head"] = "padding: 0px; justify-content: space-between; align-items: center; gap: 16px; @media(--mobile){ flex-direction: column; align-items: flex-start; }"
    st["Index label row"] = "padding: 0px; width: auto; gap: 14px; align-items: center;"
    cl["Index label row"] = ["on-eyebrow-row"]
    st["Index rule"] = f"width: 32px; min-width: 32px; height: 2px; padding: 0px; background-image: {GOLD};"
    cl["Index rule"] = ["on-rule"]
    cfg["Index label"] = {"paragraph": "Jump to a collection"}
    st["Index label"] = "font-family: var(--on-sans); font-weight: 600; font-size: 12px; letter-spacing: 3px; text-transform: uppercase; color: #C9962E; line-height: 1.2;"
    cfg["Index meta"] = {"paragraph": "12 collections&nbsp;&nbsp;·&nbsp;&nbsp;19+ brands&nbsp;&nbsp;·&nbsp;&nbsp;Pre-opening &amp; replenishment"}
    st["Index meta"] = "font-family: var(--on-sans); font-weight: 500; font-size: 13px; letter-spacing: 0.4px; color: #5F6675; line-height: 1.25; @media(--mobile){ font-size: 12px; }"
    st["Index chips"] = "padding: 0px; flex-wrap: wrap; gap: 12px; align-items: flex-start; @media(--mobile){ gap: 8px; }"
    for i, c in enumerate(COLL, 1):
        k = f"Chip {i:02d}"
        on = i == 1
        x.append(f'<e-flexbox configuration-id="{k}"><e-paragraph configuration-id="{k} num"></e-paragraph><e-paragraph configuration-id="{k} name"></e-paragraph></e-flexbox>')
        cfg[k] = {"link": link(BASE + "products/#" + c["slug"]) if USE_IDS else link(BASE + "products/")}
        st[k] = ("width: auto; gap: 10px; align-items: center; padding: 13px 20px 13px 16px; border-width: 1px; border-style: solid; transition: all 0.3s;"
                 + (" background: #0B2F62; border-color: #0B2F62;" if on else " background: #FFFFFF; border-color: #E6E0D6; &:hover{ border-color: #0B2F62; }")
                 + " @media(--mobile){ padding: 9px 12px 9px 10px; gap: 6px; }")
        cfg[f"{k} num"] = {"paragraph": f"{i:02d}"}
        st[f"{k} num"] = f"font-family: var(--on-serif); font-weight: 600; font-size: 16px; line-height: 1.2; color: {'#E2BB6C' if on else '#C9962E'};"
        cfg[f"{k} name"] = {"paragraph": c["name"]}
        st[f"{k} name"] = f"font-family: var(--on-sans); font-weight: 600; font-size: 11px; letter-spacing: 1.4px; text-transform: uppercase; line-height: 1.2; color: {'#FFFFFF' if on else '#0B2F62'}; @media(--mobile){{ font-size: 10px; letter-spacing: 1px; }}"
    x.append('</e-flexbox></e-flexbox>')
    return {"xml_structure": "".join(x), "element_config": cfg, "style": st, "classes": cl}


def collection(i, c):
    odd = i % 2 == 1
    bg = "#FFFFFF" if odd else "#F7F4EE"
    chip_bg = "#F7F4EE" if odd else "#FFFFFF"
    tag_bg = "#071F44" if odd else "#0B2F62"
    img = c.get("img", c["slug"])
    p = f"C{i:02d}"
    x, cfg, st, cl = [], {}, {}, {}
    sec = f"{p} {c['name']}"
    x.append(f'<e-flexbox configuration-id="{sec}"><e-div-block configuration-id="{p} ring"></e-div-block>')
    cfg[sec] = {"tag": "section"}
    if USE_IDS:
        cfg[sec]["_cssid"] = c["slug"]
    st[sec] = (f"background: {bg}; padding: 110px 100px; gap: 88px; align-items: center; position: relative; overflow: hidden; scroll-margin-top: 140px;"
               " @media(--tablet){ flex-direction: column; align-items: stretch; padding: 88px 40px; gap: 48px; }"
               " @media(--mobile){ flex-direction: column; align-items: stretch; padding: 64px 20px; gap: 36px; scroll-margin-top: 170px; }")
    ring_pos = "left: -200px;" if odd else "left: 1180px;"
    ring_tab = "" if odd else " @media(--tablet){ left: auto; right: -200px; }"
    st[f"{p} ring"] = RING + f"top: -140px; {ring_pos} width: 420px; height: 420px; min-width: 420px; border-width: 1.1px; border-color: rgba(201,150,46,0.3);{ring_tab} @media(--mobile){{ display: none; }}"

    def mosaic():
        x.append(f'<e-flexbox configuration-id="{p} mosaic"><e-div-block configuration-id="{p} main wrap"><e-image configuration-id="{p} main image"></e-image>'
                 f'<e-flexbox configuration-id="{p} tag"><e-paragraph configuration-id="{p} tag text"></e-paragraph></e-flexbox></e-div-block>'
                 f'<e-flexbox configuration-id="{p} thumbs"><e-image configuration-id="{p} thumb 1"></e-image><e-image configuration-id="{p} thumb 2"></e-image></e-flexbox></e-flexbox>')
        st[f"{p} mosaic"] = ("flex-direction: column; gap: 20px; padding: 0px; width: 660px; min-width: 660px; flex-shrink: 0; z-index: 1;"
                             " @media(--tablet){ width: 100%; min-width: 0px; order: -1; } @media(--mobile){ width: 100%; min-width: 0px; order: -1; gap: 12px; }")
        st[f"{p} main wrap"] = "position: relative; padding: 0px; width: 100%; height: 420px; overflow: hidden; @media(--mobile){ height: 240px; }"
        cfg[f"{p} main image"] = {"image": {"src": {"id": mid(f"products-{img}-main.jpg")}, "size": "large"}}
        st[f"{p} main image"] = "display: block; width: 100%; height: 420px; object-fit: cover; @media(--mobile){ height: 240px; }"
        st[f"{p} tag"] = f"position: absolute; top: 0px; left: 0px; width: auto; padding: 12px 18px; background: {tag_bg}; z-index: 2;"
        cfg[f"{p} tag text"] = {"paragraph": f"{i:02d} / 12"}
        st[f"{p} tag text"] = "font-family: var(--on-serif); font-weight: 600; font-size: 20px; line-height: 1.2; color: #E2BB6C;"
        st[f"{p} thumbs"] = "padding: 0px; gap: 20px; align-items: flex-start; @media(--mobile){ gap: 12px; }"
        for t in (1, 2):
            cfg[f"{p} thumb {t}"] = {"image": {"src": {"id": mid(f"products-{img}-thumb{t}.jpg")}, "size": "large"}}
            st[f"{p} thumb {t}"] = "display: block; flex: 1 1 0px; min-width: 0px; width: 50%; height: 240px; object-fit: cover; @media(--mobile){ height: 150px; }"

    def details():
        x.append(f'<e-flexbox configuration-id="{p} details"><e-flexbox configuration-id="{p} label row"><e-div-block configuration-id="{p} rule"></e-div-block>'
                 f'<e-paragraph configuration-id="{p} label"></e-paragraph></e-flexbox><e-heading configuration-id="{p} title"></e-heading>'
                 f'<e-paragraph configuration-id="{p} desc"></e-paragraph><e-flexbox configuration-id="{p} specs">')
        st[f"{p} details"] = "flex-direction: column; gap: 24px; padding: 0px; flex: 1 1 0px; min-width: 0px; align-items: flex-start; z-index: 1; @media(--tablet){ width: 100%; } @media(--mobile){ width: 100%; gap: 20px; }"
        st[f"{p} label row"] = "padding: 0px; width: auto; gap: 14px; align-items: center;"
        cl[f"{p} label row"] = ["on-eyebrow-row"]
        st[f"{p} rule"] = f"width: 32px; min-width: 32px; height: 2px; padding: 0px; background-image: {GOLD};"
        cl[f"{p} rule"] = ["on-rule"]
        cfg[f"{p} label"] = {"paragraph": f"Collection {i:02d}"}
        st[f"{p} label"] = "font-family: var(--on-sans); font-weight: 600; font-size: 12px; letter-spacing: 3px; text-transform: uppercase; color: #C9962E; line-height: 1.2;"
        cfg[f"{p} title"] = {"title": c["name"], "tag": "h2"}
        st[f"{p} title"] = "font-family: var(--on-serif); font-weight: 600; font-size: 58px; line-height: 60px; letter-spacing: -0.3px; color: #0B2F62; width: 100%; @media(--tablet){ font-size: 50px; line-height: 54px; } @media(--mobile){ font-size: 38px; line-height: 42px; }"
        cfg[f"{p} desc"] = {"paragraph": c["desc"]}
        st[f"{p} desc"] = "font-family: var(--on-sans); font-weight: 400; font-size: 16px; line-height: 28px; color: #5F6675; width: 100%; @media(--mobile){ font-size: 15px; line-height: 26px; }"
        st[f"{p} specs"] = ("padding: 24px 0px; gap: 32px; align-items: flex-start; width: 100%; border-width: 1px 0px 1px 0px; border-style: solid; border-color: #E6E0D6;"
                            " @media(--mobile){ gap: 16px; padding: 20px 0px; }")
        for key, title, items in (("range", "Range includes", c["rng"]), ("ideal", "Ideal for", c["ideal"])):
            col = f"{p} {key}"
            x.append(f'<e-flexbox configuration-id="{col}"><e-paragraph configuration-id="{col} title"></e-paragraph>')
            st[col] = "flex-direction: column; gap: 12px; padding: 0px; flex: 1 1 0px; min-width: 0px; align-items: flex-start;"
            cfg[f"{col} title"] = {"paragraph": title}
            st[f"{col} title"] = "font-family: var(--on-sans); font-weight: 600; font-size: 11px; letter-spacing: 2.2px; text-transform: uppercase; color: #0B2F62; line-height: 1.2; @media(--mobile){ font-size: 10px; letter-spacing: 1.6px; }"
            for j, it in enumerate(items, 1):
                row = f"{col} {j}"
                x.append(f'<e-flexbox configuration-id="{row}"><e-div-block configuration-id="{row} dash"></e-div-block><e-paragraph configuration-id="{row} text"></e-paragraph></e-flexbox>')
                st[row] = "padding: 0px; gap: 12px; align-items: center; width: 100%; @media(--mobile){ gap: 8px; }"
                st[f"{row} dash"] = f"width: 10px; min-width: 10px; height: 1.5px; padding: 0px; background-image: {GOLD};"
                cfg[f"{row} text"] = {"paragraph": it}
                st[f"{row} text"] = "font-family: var(--on-sans); font-weight: 400; font-size: 14px; line-height: 22px; color: #16213A; flex: 1 1 0px; min-width: 0px; @media(--mobile){ font-size: 13px; line-height: 20px; }"
            x.append('</e-flexbox>')
        x.append(f'</e-flexbox><e-flexbox configuration-id="{p} brands"><e-paragraph configuration-id="{p} brands title"></e-paragraph><e-flexbox configuration-id="{p} brand chips">')
        st[f"{p} brands"] = "flex-direction: column; gap: 12px; padding: 0px; width: 100%; align-items: flex-start;"
        cfg[f"{p} brands title"] = {"paragraph": "Featured brands"}
        st[f"{p} brands title"] = "font-family: var(--on-sans); font-weight: 600; font-size: 11px; letter-spacing: 2.2px; text-transform: uppercase; color: #0B2F62; line-height: 1.2;"
        st[f"{p} brand chips"] = "padding: 0px; flex-wrap: wrap; gap: 8px; align-items: flex-start; width: 100%;"
        for j, b in enumerate(c["brands"], 1):
            k = f"{p} brand {j}"
            x.append(f'<e-paragraph configuration-id="{k}"></e-paragraph>')
            cfg[k] = {"paragraph": b}
            st[k] = f"font-family: var(--on-sans); font-weight: 500; font-size: 12px; line-height: 15px; color: #0B2F62; background: {chip_bg}; padding: 8px 14px;"
        x.append(f'</e-flexbox></e-flexbox><e-flexbox configuration-id="{p} actions"><e-button configuration-id="{p} quote"></e-button><e-button configuration-id="{p} catalogue"></e-button></e-flexbox></e-flexbox>')
        st[f"{p} actions"] = "padding: 8px 0px 0px 0px; width: auto; gap: 14px; align-items: flex-start; flex-wrap: wrap; @media(--mobile){ gap: 10px; width: 100%; flex-wrap: nowrap; }"
        cfg[f"{p} quote"] = {"text": "Request a quote&nbsp;&nbsp;→", "link": link(BASE + "contact-us/")}
        cl[f"{p} quote"] = ["on-btn-navy"]
        st[f"{p} quote"] = "@media(--mobile){ padding: 15px 12px; font-size: 10px; letter-spacing: 1.2px; flex: 1 1 auto; justify-content: center; text-align: center; }"
        cfg[f"{p} catalogue"] = {"text": "Catalogue&nbsp;&nbsp;→", "link": link(BASE + "contact-us/")}
        cl[f"{p} catalogue"] = ["on-btn-outline-navy"]
        st[f"{p} catalogue"] = "@media(--mobile){ padding: 15px 12px; font-size: 10px; letter-spacing: 1.2px; flex: 1 1 auto; justify-content: center; text-align: center; }"

    if odd:
        mosaic(); details()
    else:
        details(); mosaic()
    x.append('</e-flexbox>')
    return {"xml_structure": "".join(x), "element_config": cfg, "style": st, "classes": cl}


def sourcing():
    x, cfg, st, cl = [], {}, {}, {}
    x.append('<e-flexbox configuration-id="Custom sourcing"><e-div-block configuration-id="CS ring outer"></e-div-block><e-div-block configuration-id="CS ring inner"></e-div-block>'
             '<e-flexbox configuration-id="CS text"><e-flexbox configuration-id="CS label row"><e-div-block configuration-id="CS rule"></e-div-block><e-paragraph configuration-id="CS label"></e-paragraph></e-flexbox>'
             '<e-heading configuration-id="CS title"></e-heading><e-paragraph configuration-id="CS lead"></e-paragraph>'
             '<e-flexbox configuration-id="CS buttons"><e-button configuration-id="CS send"></e-button><e-button configuration-id="CS call"></e-button></e-flexbox></e-flexbox>'
             '<e-flexbox configuration-id="CS services">')
    cfg["Custom sourcing"] = {"tag": "section"}
    st["Custom sourcing"] = ("background: #071F44; padding: 110px 100px; gap: 80px; align-items: center; position: relative; overflow: hidden; min-height: 620px;"
                             " @media(--tablet){ flex-direction: column; align-items: stretch; padding: 88px 40px; gap: 48px; min-height: 0px; }"
                             " @media(--mobile){ flex-direction: column; align-items: stretch; padding: 64px 20px; gap: 40px; min-height: 0px; }")
    st["CS ring outer"] = RING + "top: -180px; left: -160px; width: 640px; height: 640px; min-width: 640px; border-width: 1.2px; border-color: rgba(201,150,46,0.35); @media(--mobile){ display: none; }"
    st["CS ring inner"] = RING + "top: -90px; left: -70px; width: 460px; height: 460px; min-width: 460px; border-width: 1px; border-color: rgba(201,150,46,0.2); @media(--mobile){ display: none; }"
    st["CS text"] = "flex-direction: column; gap: 24px; padding: 0px; flex: 1 1 0px; min-width: 0px; align-items: flex-start; z-index: 1;"
    st["CS label row"] = "padding: 0px; width: auto; gap: 14px; align-items: center;"
    cl["CS label row"] = ["on-eyebrow-row"]
    st["CS rule"] = f"width: 32px; min-width: 32px; height: 2px; padding: 0px; background-image: {GOLD};"
    cl["CS rule"] = ["on-rule"]
    cfg["CS label"] = {"paragraph": "Custom sourcing"}
    st["CS label"] = "font-family: var(--on-sans); font-weight: 600; font-size: 12px; letter-spacing: 3px; text-transform: uppercase; color: #E2BB6C; line-height: 1.2;"
    cfg["CS title"] = {"title": "Can’t find exactly what you need?", "tag": "h2"}
    st["CS title"] = "font-family: var(--on-serif); font-weight: 600; font-size: 60px; line-height: 62px; letter-spacing: -0.3px; color: #FFFFFF; width: 100%; @media(--tablet){ font-size: 50px; line-height: 54px; } @media(--mobile){ font-size: 38px; line-height: 42px; }"
    cfg["CS lead"] = {"paragraph": "Matching an existing pattern, sourcing a special finish or equipping a new concept from scratch — our team will find it, sample it and deliver it."}
    st["CS lead"] = "font-family: var(--on-sans); font-weight: 400; font-size: 17px; line-height: 30px; color: rgba(255,255,255,0.78); width: 100%; @media(--mobile){ font-size: 15px; line-height: 26px; }"
    st["CS buttons"] = "padding: 0px; width: auto; gap: 16px; align-items: flex-start; flex-wrap: wrap; @media(--mobile){ flex-direction: column; align-items: stretch; width: 100%; gap: 12px; }"
    cfg["CS send"] = {"text": "Send your requirements&nbsp;&nbsp;→", "link": link(BASE + "contact-us/")}
    cl["CS send"] = ["on-btn-gold"]
    cfg["CS call"] = {"text": "Call +971 50 203 8005&nbsp;&nbsp;→", "link": link("tel:+971502038005")}
    cl["CS call"] = ["on-btn-outline-light"]
    st["CS services"] = "flex-direction: column; padding: 0px; width: 480px; min-width: 480px; flex-shrink: 0; gap: 0px; z-index: 1; @media(--tablet){ width: 100%; min-width: 0px; } @media(--mobile){ width: 100%; min-width: 0px; }"
    for j, (t, d) in enumerate([("Pre-opening packages", "Complete operating-equipment lists for new hotels and outlets."),
                                ("Pattern matching", "Replacements and top-ups for existing tableware."),
                                ("Custom branding", "Logo-printed, engraved and bespoke pieces."),
                                ("Samples &amp; trials", "Try key items on your tables before you order.")], 1):
        k = f"CS service {j}"
        x.append(f'<e-flexbox configuration-id="{k}"><e-heading configuration-id="{k} title"></e-heading><e-paragraph configuration-id="{k} text"></e-paragraph></e-flexbox>')
        st[k] = "flex-direction: column; gap: 6px; padding: 22px 0px; width: 100%; align-items: flex-start; border-width: 0px 0px 1px 0px; border-style: solid; border-color: rgba(255,255,255,0.14);"
        cfg[f"{k} title"] = {"title": t, "tag": "h3"}
        st[f"{k} title"] = "font-family: var(--on-serif); font-weight: 600; font-size: 26px; line-height: 27px; letter-spacing: -0.3px; color: #FFFFFF; @media(--mobile){ font-size: 23px; line-height: 26px; }"
        cfg[f"{k} text"] = {"paragraph": d}
        st[f"{k} text"] = "font-family: var(--on-sans); font-weight: 400; font-size: 14px; line-height: 22px; color: rgba(255,255,255,0.7);"
    x.append('</e-flexbox></e-flexbox>')
    return {"xml_structure": "".join(x), "element_config": cfg, "style": st, "classes": cl}


if __name__ == "__main__":
    save(1, "hero", hero())
    save(2, "collection-index", index())
    for i, c in enumerate(COLL, 1):
        save(2 + i, c["slug"], collection(i, c))
    save(15, "custom-sourcing", sourcing())
