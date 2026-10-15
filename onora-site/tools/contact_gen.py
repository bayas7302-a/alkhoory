#!/usr/bin/env python3
"""Generate the CONTACT page (post 29) section payloads into sections/contact-NN-*.json."""
import json, os

PID = 29
OUT = "/home/user/alkhoory/onora-site/sections"
BASE = "https://soharon.co.uk/onora/"
GOLD = "linear-gradient(90deg, #A5740B 0%, #DEAF57 25%, #F1C96A 50%, #BE8826 75%, #A5740B 100%)"
HERO_IMG = "https://soharon.co.uk/onora/wp-content/uploads/2026/10/contact-hero-tableware.jpg"
MAP_IMG = "https://soharon.co.uk/onora/wp-content/uploads/2026/10/contact-map-plate.jpg"
SERIF = "font-family: var(--on-serif); font-weight: 600;"
SANS = "font-family: var(--on-sans);"
SIDE = " @media(--tablet){ padding-left: 40px; padding-right: 40px; } @media(--mobile){ padding-left: 20px; padding-right: 20px; }"


def link(url, blank=False):
    return {"destination": url, "isTargetBlank": blank, "tag": "a"}


class C:
    def __init__(self):
        self.x, self.cfg, self.st, self.cl = [], {}, {}, {}

    def open(self, tag, cid, style=None, cfg=None, cls=None):
        self.x.append(f'<{tag} configuration-id="{cid}">')
        self._set(cid, style, cfg, cls)

    def close(self, tag):
        self.x.append(f'</{tag}>')

    def leaf(self, tag, cid, style=None, cfg=None, cls=None):
        self.x.append(f'<{tag} configuration-id="{cid}"></{tag}>')
        self._set(cid, style, cfg, cls)

    def p(self, cid, text, style, url=None, blank=False, tag=None):
        cfg = {"paragraph": text}
        if url:
            cfg["link"] = link(url, blank)
        if tag:
            cfg["tag"] = tag
        self.leaf("e-paragraph", cid, style, cfg)

    def _set(self, cid, style, cfg, cls):
        if style:
            self.st[cid] = style
        if cfg:
            self.cfg[cid] = cfg
        if cls:
            self.cl[cid] = cls

    def eyebrow(self, pre, text):
        self.open("e-flexbox", f"{pre} label", "padding: 0px; width: auto; gap: 14px; align-items: center;")
        self.leaf("e-div-block", f"{pre} rule", f"width: 32px; min-width: 32px; height: 2px; padding: 0px; background: {GOLD};")
        self.p(f"{pre} eyebrow", text, f"{SANS} font-weight: 600; font-size: 12px; letter-spacing: 3px; text-transform: uppercase; color: #C9962E;")
        self.close("e-flexbox")

    def payload(self):
        return {"post_id": PID, "xml_structure": "".join(self.x), "element_config": self.cfg, "style": self.st, "classes": self.cl}


def ring(c, cid, size, pos, opacity, width="1.2px"):
    c.leaf("e-div-block", cid, f"position: absolute; {pos} width: {size}px; height: {size}px; min-width: {size}px; padding: 0px;"
           f" border: {width} solid rgba(222,175,87,{opacity}); border-radius: 50%;")


# ---------------------------------------------------------------- 01 hero
def hero():
    c = C()
    c.open("e-flexbox", "Contact hero",
           f"position: relative; overflow: hidden; min-height: 560px; padding: 162px 100px 120px 100px; flex-direction: column; justify-content: flex-start;"
           f" background: linear-gradient(90deg, rgba(7,31,68,0.96) 0%, rgba(11,47,98,0.78) 55%, rgba(11,47,98,0.3) 100%), url({HERO_IMG}) center / cover no-repeat, #0B2F62;"
           " @media(--tablet){ min-height: 480px; padding: 120px 40px 100px 40px; } @media(--mobile){ min-height: 0px; padding: 80px 20px 72px 20px; }")
    ring(c, "Hero ring large", 620, "top: -120px; left: 980px;", 0.45)
    c.st["Hero ring large"] += " @media(--tablet){ left: auto; right: -260px; } @media(--mobile){ display: none; }"
    ring(c, "Hero ring small", 470, "top: -45px; left: 1055px;", 0.25, "1px")
    c.st["Hero ring small"] += " @media(--tablet){ left: auto; right: -185px; } @media(--mobile){ display: none; }"
    c.open("e-flexbox", "Hero content", "position: relative; z-index: 2; flex-direction: column; gap: 24px; padding: 0px; max-width: 780px; align-items: flex-start;")
    c.p("Hero breadcrumb", f'<a href="{BASE}" style="color:inherit;text-decoration:none">Home</a>&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;Contact Us',
        f"{SANS} font-weight: 500; font-size: 12px; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,0.6);")
    c.leaf("e-heading", "Hero title", f"{SERIF} font-size: 88px; line-height: 88px; letter-spacing: -0.3px; color: #FFFFFF;"
           " @media(--tablet){ font-size: 68px; line-height: 70px; } @media(--mobile){ font-size: 46px; line-height: 50px; }",
           {"tag": "h1", "title": "Let’s talk hospitality."})
    c.leaf("e-div-block", "Hero rule", f"width: 90px; min-width: 90px; height: 2px; padding: 0px; background: {GOLD};")
    c.p("Hero lead", "Planning an opening, refreshing an outlet or looking for a specific product? Our team in Dubai is ready to help.",
        f"{SANS} font-weight: 400; font-size: 17px; line-height: 30px; color: rgba(255,255,255,0.82); max-width: 560px; @media(--mobile){{ font-size: 15px; line-height: 26px; }}")
    c.close("e-flexbox")
    c.close("e-flexbox")
    return c.payload()


# ---------------------------------------------------------------- 02 quick contact
def quick():
    c = C()
    c.open("e-flexbox", "Quick contact", "background: #FFFFFF; padding: 80px 100px 0px 100px; gap: 24px; align-items: stretch;"
           " @media(--tablet){ flex-wrap: wrap; padding: 64px 40px 0px 40px; } @media(--mobile){ flex-direction: column; padding: 48px 20px 0px 20px; gap: 16px; }")
    cards = [("Call us", "01", "+971 50 203 8005", "Sun – Fri, 9:00 – 18:00", "tel:+971502038005", True),
             ("Email us", "02", "jason@onorahs.com", "We reply within one working day", "mailto:jason@onorahs.com", False),
             ("Visit us", "03", "Office M-A98, Hamra Building", "Karama, Dubai — UAE", None, False),
             ("Website", "04", "onorahs.com", "Catalogues &amp; brand updates", "https://onorahs.com", False)]
    for label, num, big, small, url, dark in cards:
        cid = f"Card {label}"
        bg = "#0B2F62" if dark else "#FFFFFF"
        bd = "#0B2F62" if dark else "#E6E0D6"
        c.open("e-flexbox", cid, f"flex-direction: column; gap: 14px; padding: 40px 32px; flex: 1 1 0px; min-width: 0px; background: {bg}; border: 1px solid {bd}; align-items: flex-start;"
               " transition: box-shadow 0.3s; &:hover{ box-shadow: 0px 18px 40px rgba(11,47,98,0.10); }"
               " @media(--tablet){ flex: 1 1 calc(50% - 12px); } @media(--mobile){ flex: 1 1 auto; padding: 32px 24px; }",
               {"link": link(url, url.startswith("http"))} if url else None)
        c.open("e-flexbox", f"{cid} badge", "width: 52px; height: 52px; min-width: 52px; padding: 0px; border: 1.2px solid #A5740B; border-radius: 26px; justify-content: center; align-items: center;")
        c.p(f"{cid} number", num, f"{SERIF} font-size: 20px; line-height: 1; color: {'#E2BB6C' if dark else '#C9962E'};")
        c.close("e-flexbox")
        c.p(f"{cid} label", label, f"{SANS} font-weight: 600; font-size: 11px; letter-spacing: 2.4px; text-transform: uppercase; color: {'#E2BB6C' if dark else '#C9962E'};")
        c.p(f"{cid} value", big, f"{SERIF} font-size: 26px; line-height: 30px; letter-spacing: -0.3px; color: {'#FFFFFF' if dark else '#0B2F62'};")
        c.p(f"{cid} note", small, f"{SANS} font-weight: 400; font-size: 14px; line-height: 22px; color: {'rgba(255,255,255,0.75)' if dark else '#5F6675'};")
        c.close("e-flexbox")
    c.close("e-flexbox")
    return c.payload()


# ---------------------------------------------------------------- 03 enquiry
LABEL = f"{SANS} font-weight: 600; font-size: 11px; letter-spacing: 1.8px; text-transform: uppercase; color: #0B2F62;"
INPUT = f"{SANS} font-weight: 400; font-size: 14px; line-height: 17px; color: rgba(95,102,117,0.8); background: #FBFAF7; border: 1px solid #E6E0D6; padding: 16px 18px; width: 100%;"


def field(c, cid, label, placeholder, tall=False):
    c.open("e-flexbox", cid, "flex-direction: column; gap: 10px; padding: 0px; flex: 1 1 0px; min-width: 0px;")
    c.p(f"{cid} label", label, LABEL)
    c.p(f"{cid} input", placeholder, INPUT + (" min-height: 140px;" if tall else ""))
    c.close("e-flexbox")


def enquiry():
    c = C()
    c.open("e-flexbox", "Enquiry", "background: #FFFFFF; flex-direction: column; padding: 110px 100px 120px 100px;"
           " @media(--tablet){ padding: 88px 40px 96px 40px; } @media(--mobile){ padding: 64px 20px 72px 20px; }")
    c.open("e-flexbox", "Enquiry row", "padding: 0px; gap: 72px; align-items: flex-start;"
           " @media(--tablet){ flex-direction: column; gap: 56px; } @media(--mobile){ flex-direction: column; gap: 48px; }")
    # details column
    c.open("e-flexbox", "Details", "flex-direction: column; gap: 28px; padding: 0px; width: 440px; min-width: 440px; align-items: flex-start;"
           " @media(--tablet){ width: 100%; min-width: 0px; } @media(--mobile){ width: 100%; min-width: 0px; }")
    c.eyebrow("Details", "Get in touch")
    c.leaf("e-heading", "Details title", f"{SERIF} font-size: 60px; line-height: 62px; letter-spacing: -0.3px; color: #0B2F62;"
           " @media(--tablet){ font-size: 50px; line-height: 54px; } @media(--mobile){ font-size: 40px; line-height: 44px; }",
           {"tag": "h2", "title": "Tell us about your venue."})
    c.p("Details lead", "Share a few details and we will come back with product recommendations, brand options and a tailored quotation — usually within one working day.",
        f"{SANS} font-weight: 400; font-size: 16px; line-height: 29px; color: #5F6675;")
    c.open("e-flexbox", "Contact person", "background: #F7F4EE; padding: 28px; gap: 20px; align-items: center; @media(--mobile){ padding: 20px; gap: 14px; }")
    c.open("e-flexbox", "Avatar", "width: 64px; height: 64px; min-width: 64px; padding: 0px; border-radius: 32px; background: #0B2F62; justify-content: center; align-items: center;")
    c.p("Avatar initials", "JD", f"{SERIF} font-size: 24px; line-height: 1; color: #E2BB6C;")
    c.close("e-flexbox")
    c.open("e-flexbox", "Person text", "flex-direction: column; gap: 4px; padding: 0px; min-width: 0px;")
    c.p("Person name", "Jason Dsouza", f"{SERIF} font-size: 28px; line-height: 29px; letter-spacing: -0.3px; color: #0B2F62;")
    c.p("Person role", "Chief Executive Officer", f"{SANS} font-weight: 600; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #C9962E;")
    c.p("Person contacts", '<a href="tel:+971502038005" style="color:inherit;text-decoration:none">+971&nbsp;50&nbsp;203&nbsp;8005</a>&nbsp;&nbsp;·&nbsp;&nbsp;'
        '<a href="mailto:jason@onorahs.com" style="color:inherit;text-decoration:none">jason@onorahs.com</a>',
        f"{SANS} font-weight: 400; font-size: 13px; line-height: 18px; color: #5F6675;")
    c.close("e-flexbox")
    c.close("e-flexbox")
    # hours
    c.open("e-flexbox", "Hours", "flex-direction: column; gap: 0px; padding: 0px;")
    c.p("Hours title", "Office hours", f"{SANS} font-weight: 600; font-size: 11px; letter-spacing: 2.2px; text-transform: uppercase; color: #0B2F62;")
    for day, time in [("Sunday – Thursday", "9:00 – 18:00"), ("Friday", "9:00 – 13:00"), ("Saturday", "By appointment")]:
        cid = f"Hours {day}"
        c.open("e-flexbox", cid, "padding: 14px 0px; justify-content: space-between; align-items: flex-start; gap: 12px; border-width: 0px 0px 1px 0px; border-style: solid; border-color: #E6E0D6;")
        c.p(f"{cid} day", day, f"{SANS} font-weight: 400; font-size: 14px; line-height: 17px; color: #16213A;")
        c.p(f"{cid} time", time, f"{SANS} font-weight: 500; font-size: 14px; line-height: 17px; color: #0B2F62;")
        c.close("e-flexbox")
    c.close("e-flexbox")
    # socials
    c.open("e-flexbox", "Social chips", "padding: 0px; gap: 8px; width: auto; flex-wrap: wrap;")
    for name, url in [("Instagram", "https://www.instagram.com/"), ("LinkedIn", "https://www.linkedin.com/"),
                      ("Facebook", "https://www.facebook.com/"), ("WhatsApp", "https://wa.me/971502038005")]:
        c.p(f"Social {name}", name, f"{SANS} font-weight: 600; font-size: 10px; line-height: 12px; letter-spacing: 1.6px; text-transform: uppercase; color: #0B2F62;"
            " padding: 10px 14px; border: 1px solid #E6E0D6; transition: border-color 0.3s, color 0.3s; &:hover{ border-color: #C9962E; color: #C9962E; }", url, True)
    c.close("e-flexbox")
    c.close("e-flexbox")
    # form card
    c.open("e-flexbox", "Enquiry form", "position: relative; flex-direction: column; gap: 24px; padding: 56px; flex: 1 1 0px; min-width: 0px; background: #FFFFFF;"
           " border: 1px solid #E6E0D6; box-shadow: 0px 24px 60px 0px rgba(11,47,98,0.08);"
           " @media(--tablet){ width: 100%; flex: 1 1 auto; padding: 48px; } @media(--mobile){ width: 100%; flex: 1 1 auto; padding: 32px 20px; }")
    c.leaf("e-div-block", "Form gold bar", f"position: absolute; top: -1px; left: -1px; right: -1px; width: auto; height: 4px; min-width: 0px; padding: 0px; background: {GOLD};")
    c.leaf("e-heading", "Form title", f"{SERIF} font-size: 40px; line-height: 42px; letter-spacing: -0.3px; color: #0B2F62; @media(--mobile){{ font-size: 32px; line-height: 36px; }}",
           {"tag": "h3", "title": "Request a quotation"})
    c.p("Form note", "Fields marked * are required.", f"{SANS} font-weight: 400; font-size: 13px; line-height: 16px; color: #5F6675;")
    row = "padding: 0px; gap: 20px; align-items: flex-start; @media(--mobile){ flex-direction: column; }"
    c.open("e-flexbox", "Form row 1", row)
    field(c, "Field name", "Full name *", "Your name")
    field(c, "Field company", "Company / venue *", "Hotel or restaurant name")
    c.close("e-flexbox")
    c.open("e-flexbox", "Form row 2", row)
    field(c, "Field email", "Email *", "name@company.com")
    field(c, "Field phone", "Phone", "+971")
    c.close("e-flexbox")
    c.open("e-flexbox", "Interests", "flex-direction: column; gap: 12px; padding: 0px;")
    c.p("Interests label", "I’m interested in", LABEL)
    c.open("e-flexbox", "Chips", "padding: 0px; gap: 8px; flex-wrap: wrap; align-items: flex-start;")
    for chip in ["Chinaware", "Cutlery", "Glassware", "Drinkware", "Melamine", "Kitchen", "Buffet", "Bar", "Trolleys", "Accessories", "Lamps", "Indian"]:
        on = chip in ("Chinaware", "Buffet")
        c.p(f"Chip {chip}", chip, f"{SANS} font-weight: 500; font-size: 12px; line-height: 15px; padding: 9px 14px;"
            + (" background: #0B2F62; color: #FFFFFF; border: 1px solid #0B2F62;" if on else " color: #0B2F62; border: 1px solid #E6E0D6;"))
    c.close("e-flexbox")
    c.close("e-flexbox")
    c.open("e-flexbox", "Form row 3", "padding: 0px;")
    field(c, "Field message", "Message", "Tell us about your project, quantities and timeline…", tall=True)
    c.close("e-flexbox")
    c.open("e-flexbox", "Submit row", "padding: 0px; justify-content: space-between; align-items: center; gap: 20px; @media(--mobile){ flex-direction: column-reverse; align-items: stretch; }")
    c.p("Privacy note", "We respect your privacy — no spam, ever.", f"{SANS} font-weight: 400; font-size: 12px; line-height: 15px; color: #5F6675;")
    c.leaf("e-button", "Send enquiry", f"background: {GOLD}; color: #071F44; {SANS} font-weight: 600; font-size: 12px; line-height: 18px; letter-spacing: 2.2px; text-transform: uppercase;"
           " padding: 18px 30px; border-radius: 0px; transition: opacity 0.3s; &:hover{ opacity: 0.9; }",
           {"text": "Send enquiry&nbsp;&nbsp;→", "link": link("mailto:jason@onorahs.com?subject=Quotation%20request%20-%20ONORA%20website")})
    c.close("e-flexbox")
    c.close("e-flexbox")
    c.close("e-flexbox")
    c.close("e-flexbox")
    return c.payload()


# ---------------------------------------------------------------- 04 map
def mapsec():
    c = C()
    c.open("e-flexbox", "Map", f"position: relative; overflow: hidden; min-height: 560px; padding: 130px 100px; align-items: center;"
           f" background: linear-gradient(0deg, rgba(7,31,68,0.72) 0%, rgba(7,31,68,0.72) 100%), url({MAP_IMG}) center / cover no-repeat, #071F44;"
           " @media(--tablet){ min-height: 480px; padding: 96px 40px; } @media(--mobile){ min-height: 0px; padding: 64px 20px 260px 20px; }")
    ring(c, "Map ring large", 500, "top: 30px; right: 120px;", 0.45)
    c.st["Map ring large"] += " @media(--tablet){ right: -120px; } @media(--mobile){ top: auto; bottom: -140px; right: -55px; }"
    ring(c, "Map ring small", 300, "top: 130px; right: 220px;", 0.25, "1px")
    c.st["Map ring small"] += " @media(--tablet){ right: -20px; } @media(--mobile){ top: auto; bottom: -40px; right: 45px; }"
    c.leaf("e-div-block", "Map pin halo", "position: absolute; top: 235px; right: 325px; width: 90px; height: 90px; min-width: 90px; padding: 0px; border-radius: 50%; background: rgba(226,187,108,0.18);"
           " @media(--tablet){ right: 85px; } @media(--mobile){ top: auto; bottom: 65px; right: 150px; }")
    c.leaf("e-div-block", "Map pin", f"position: absolute; top: 266px; right: 356px; width: 28px; height: 28px; min-width: 28px; padding: 0px; border-radius: 50%; border: 5px solid #FFFFFF; background: {GOLD};"
           " @media(--tablet){ right: 116px; } @media(--mobile){ top: auto; bottom: 96px; right: 181px; }")
    c.open("e-flexbox", "Location card", "position: relative; z-index: 2; flex-direction: column; gap: 18px; padding: 44px; width: 440px; max-width: 100%; background: #FFFFFF; align-items: flex-start;"
           " @media(--mobile){ padding: 32px 24px; }")
    c.eyebrow("Location", "Find us")
    c.leaf("e-heading", "Location title", f"{SERIF} font-size: 36px; line-height: 37px; letter-spacing: -0.3px; color: #0B2F62; @media(--mobile){{ font-size: 30px; line-height: 32px; }}",
           {"tag": "h2", "title": "ONORA Hotel Supplies"})
    c.p("Location address", "Office M-A98, Hamra Building<br>Karama, Dubai — United Arab Emirates", f"{SANS} font-weight: 400; font-size: 15px; line-height: 26px; color: #5F6675;")
    c.leaf("e-button", "Get directions", f"background: #0B2F62; color: #FFFFFF; {SANS} font-weight: 600; font-size: 12px; line-height: 18px; letter-spacing: 2.2px; text-transform: uppercase;"
           " padding: 18px 30px; border-radius: 0px; transition: background-color 0.3s; &:hover{ background: #071F44; }",
           {"text": "Get directions&nbsp;&nbsp;→", "link": link("https://www.google.com/maps/search/?api=1&query=Hamra+Building+Karama+Dubai", True)})
    c.close("e-flexbox")
    c.close("e-flexbox")
    return c.payload()


# ---------------------------------------------------------------- 05 faq
FAQ = [("Do you supply outside Dubai?", "Yes — we deliver across all seven emirates and can support projects in the wider GCC on request."),
       ("Can I order samples before committing?", "Absolutely. We offer samples upon request to help you make confident purchasing decisions."),
       ("Do you handle full pre-opening packages?", "Yes — we can bring chinaware, cutlery, glassware, buffet, bar and service equipment together in one tailored proposal for your opening. Share your outlet list and timeline and we will take it from there."),
       ("Can you add our logo to tableware?", "Custom branding depends on the brand, product and quantity. Tell us what you have in mind and we will advise on the available options."),
       ("How quickly will I receive a quotation?", "Usually within one working day of receiving your enquiry, depending on the size of the request.")]


def faq():
    c = C()
    c.open("e-flexbox", "FAQ", "background: #F7F4EE; flex-direction: column; padding: 120px 100px;"
           " @media(--tablet){ padding: 96px 40px; } @media(--mobile){ padding: 72px 20px; }")
    c.open("e-flexbox", "FAQ row", "padding: 0px; gap: 96px; align-items: flex-start;"
           " @media(--tablet){ flex-direction: column; gap: 48px; } @media(--mobile){ flex-direction: column; gap: 36px; }")
    c.open("e-flexbox", "FAQ intro", "flex-direction: column; gap: 24px; padding: 0px; width: 400px; min-width: 400px; align-items: flex-start;"
           " @media(--tablet){ width: 100%; min-width: 0px; } @media(--mobile){ width: 100%; min-width: 0px; }")
    c.eyebrow("FAQ", "Good to know")
    c.leaf("e-heading", "FAQ title", f"{SERIF} font-size: 56px; line-height: 58px; letter-spacing: -0.3px; color: #0B2F62;"
           " @media(--tablet){ font-size: 48px; line-height: 52px; } @media(--mobile){ font-size: 40px; line-height: 44px; }",
           {"tag": "h2", "title": "Frequently asked questions."})
    c.p("FAQ lead", 'Anything else? <a href="tel:+971502038005" style="color:inherit">Call</a> or <a href="mailto:jason@onorahs.com" style="color:inherit">email</a> us and we will be happy to help.',
        f"{SANS} font-weight: 400; font-size: 16px; line-height: 28px; color: #5F6675;")
    c.close("e-flexbox")
    c.open("e-accordion", "FAQ accordion", "flex: 1 1 0px; min-width: 0px; padding: 0px; gap: 0px; @media(--tablet){ width: 100%; flex: 1 1 auto; } @media(--mobile){ width: 100%; flex: 1 1 auto; }",
           {"default_state": "first_expanded", "max_expanded": "one", "show_icon": True, "faq_schema": True})
    for i, (q, a) in enumerate(FAQ, 1):
        it = f"Q{i}"
        c.open("e-accordion-item", it, "padding: 0px; border-width: 0px 0px 1px 0px; border-style: solid; border-color: #E6E0D6;")
        c.open("e-accordion-item-header", f"{it} header", "padding: 28px 0px; gap: 20px; justify-content: space-between; align-items: center;")
        c.open("e-accordion-item-title", f"{it} title wrap", "padding: 0px;")
        c.p(f"{it} question", q, f"{SERIF} font-size: 28px; line-height: 32px; letter-spacing: -0.3px; color: #0B2F62; @media(--mobile){{ font-size: 22px; line-height: 27px; }}")
        c.close("e-accordion-item-title")
        c.open("e-accordion-item-icon", f"{it} icon wrap", "width: auto; height: auto; padding: 0px;")
        c.p(f"{it} icon", "+", f"{SANS} font-weight: 500; font-size: 24px; line-height: 1; color: #C9962E;")
        c.close("e-accordion-item-icon")
        c.close("e-accordion-item-header")
        c.open("e-accordion-item-content", f"{it} content", "padding: 0px 0px 28px 0px;")
        c.p(f"{it} answer", a, f"{SANS} font-weight: 400; font-size: 15px; line-height: 27px; color: #5F6675;")
        c.close("e-accordion-item-content")
        c.close("e-accordion-item")
    c.close("e-accordion")
    c.close("e-flexbox")
    c.close("e-flexbox")
    return c.payload()


if __name__ == "__main__":
    for n, (name, fn) in enumerate([("hero", hero), ("quick-contact", quick), ("enquiry", enquiry), ("map", mapsec), ("faq", faq)], 1):
        f = os.path.join(OUT, f"contact-{n:02d}-{name}.json")
        json.dump(fn(), open(f, "w"), indent=1, ensure_ascii=False)
        print(f)
