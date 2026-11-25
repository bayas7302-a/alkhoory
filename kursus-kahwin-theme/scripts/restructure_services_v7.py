"""v7: restructure the services section (af68fa7) into header + two balanced rows.
Usage: python3 restructure_services_v7.py <in.json> <out.json>"""
import json, sys, secrets

def nid(): return secrets.token_hex(4)[:7]

def find(els, i, parent=None):
    for e in els:
        if e["id"] == i: return e, parent
        r = find(e.get("elements", []), i, e)
        if r[0]: return r
    return None, None

def box(cls, children):
    return {"id": nid(), "elType": "container", "isInner": True,
            "settings": {"content_width": "full", "css_classes": cls, "_css_classes": cls}, "elements": children}

def text(cls, html):
    return {"id": nid(), "elType": "widget", "widgetType": "text-editor", "isInner": False,
            "settings": {"editor": html, "_css_classes": cls}, "elements": []}

S1_MAIN = """<div class="kk-svc">
<div class="kk-svc-head"><span class="kk-svc-num">01</span><h3 class="kk-svc-title">Kursus Pra Perkahwinan Islam</h3></div>
<p>Kami merupakan penyedia perkhidmatan pengurusan dan pendaftaran Kursus Pra Perkahwinan Islam yang menawarkan pilihan lokasi kursus di sekitar Kuala Lumpur, TTDI Damansara dan Putrajaya.</p>
<p>Dengan rangkaian lokasi kursus yang tersedia, kami memudahkan bakal pengantin untuk membuat pendaftaran serta memilih lokasi dan tarikh kursus yang bersesuaian dengan keperluan masing-masing.</p>
</div>"""
S1_LIST = """<h4 class="kk-svc-sub">Mengapa Memilih Kami?</h4>
<ul class="kk-list">
<li>Menguruskan beberapa lokasi kursus kahwin sekitar Kuala Lumpur, TTDI Damansara dan Putrajaya.</li>
<li>Pilihan tarikh dan lokasi kursus yang fleksibel untuk kemudahan peserta.</li>
<li>Pengurusan pendaftaran dan penyelarasan peserta secara sistematik.</li>
<li>Kursus dikendalikan oleh tenaga pengajar dan fasilitator yang berpengalaman.</li>
<li>Penyelarasan sijil kursus bagi tujuan permohonan perkahwinan mengikut ketetapan pihak berkuasa agama.</li>
</ul>"""
S2 = """<div class="kk-svc">
<div class="kk-svc-head"><span class="kk-svc-num">02</span><h3 class="kk-svc-title">Bantuan Borang Perkahwinan</h3></div>
<p>Kami turut menyediakan khidmat bantuan pengisian dan semakan borang permohonan perkahwinan bagi membantu bakal pengantin menguruskan dokumentasi dengan lebih mudah, teratur dan lancar.</p>
<h4 class="kk-svc-sub">Perkhidmatan ini merangkumi:</h4>
<ul class="kk-list">
<li><strong>Bantuan Pengisian Borang Nikah</strong> – Bantuan pengisian borang permohonan perkahwinan melalui sistem berkaitan seperti SPPIM, e-Munakahat dan sistem permohonan negeri yang berkenaan.</li>
<li><strong>Semakan Borang Permohonan</strong> – Membantu menyemak maklumat dan kelengkapan borang bagi memastikan dokumen disediakan mengikut keperluan permohonan.</li>
</ul>
</div>
<p class="kk-tagline">Satu tempat untuk membantu anda menguruskan persediaan perkahwinan dengan lebih mudah, teratur dan yakin.</p>"""

data = json.load(open(sys.argv[1]))
sec, _ = find(data, "af68fa7")
heading, _ = find(data, "1e533a5")
button, _ = find(data, "f918ead")
poster, _ = find(data, "52cfbbd")
assert heading and button and poster, "expected widgets missing"

sec["elements"] = [
    box("kk-v7-header", [heading]),
    box("kk-v7-row kk-v7-row1", [
        box("kk-v7-col kk-v7-s1", [text("kk-services kk-v7-text", S1_MAIN)]),
        box("kk-v7-col kk-v7-panel", [text("kk-services kk-v7-list", S1_LIST)]),
    ]),
    box("kk-v7-row kk-v7-row2", [
        box("kk-v7-col kk-v7-poster", [poster]),
        box("kk-v7-col kk-v7-s2", [text("kk-services kk-v7-text", S2), button]),
    ]),
]
s = sec.setdefault("settings", {})
s["flex_direction"] = "column"; s["flex_direction_tablet"] = "column"; s["flex_direction_mobile"] = "column"
s["flex_wrap_mobile"] = "nowrap"
json.dump(data, open(sys.argv[2], "w"), ensure_ascii=False)
print("ok")
