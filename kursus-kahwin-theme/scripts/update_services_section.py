"""Replace the 'Tentang Kami' section content with the services write-up + poster.
Usage: python3 update_services_section.py <in_elementor_data.json> <out.json>"""
import json, sys, secrets

WA = ("https://wa.me/60147184475?text=Assalamualaikum%2C%20saya%20ingin%20bertanya%20tentang%20"
      "Kursus%20Pra%20Perkahwinan%20dan%20Bantuan%20Borang%20Perkahwinan.")

SERVICES_HTML = """<div class="kk-svc">
<div class="kk-svc-head"><span class="kk-svc-num">01</span><h3 class="kk-svc-title">Kursus Pra Perkahwinan Islam</h3></div>
<p>Kami merupakan penyedia perkhidmatan pengurusan dan pendaftaran Kursus Pra Perkahwinan Islam yang menawarkan pilihan lokasi kursus di sekitar Kuala Lumpur, TTDI Damansara dan Putrajaya.</p>
<p>Dengan rangkaian lokasi kursus yang tersedia, kami memudahkan bakal pengantin untuk membuat pendaftaran serta memilih lokasi dan tarikh kursus yang bersesuaian dengan keperluan masing-masing.</p>
<h4 class="kk-svc-sub">Mengapa Memilih Kami?</h4>
<ul class="kk-list">
<li>Menguruskan beberapa lokasi kursus kahwin sekitar Kuala Lumpur, TTDI Damansara dan Putrajaya.</li>
<li>Pilihan tarikh dan lokasi kursus yang fleksibel untuk kemudahan peserta.</li>
<li>Pengurusan pendaftaran dan penyelarasan peserta secara sistematik.</li>
<li>Kursus dikendalikan oleh tenaga pengajar dan fasilitator yang berpengalaman.</li>
<li>Penyelarasan sijil kursus bagi tujuan permohonan perkahwinan mengikut ketetapan pihak berkuasa agama.</li>
</ul>
</div>
<div class="kk-svc">
<div class="kk-svc-head"><span class="kk-svc-num">02</span><h3 class="kk-svc-title">Bantuan Borang Perkahwinan</h3></div>
<p>Kami turut menyediakan khidmat bantuan pengisian dan semakan borang permohonan perkahwinan bagi membantu bakal pengantin menguruskan dokumentasi dengan lebih mudah, teratur dan lancar.</p>
<h4 class="kk-svc-sub">Perkhidmatan ini merangkumi:</h4>
<ul class="kk-list">
<li><strong>Bantuan Pengisian Borang Nikah</strong> – Bantuan pengisian borang permohonan perkahwinan melalui sistem berkaitan seperti SPPIM, e-Munakahat dan sistem permohonan negeri yang berkenaan.</li>
<li><strong>Semakan Borang Permohonan</strong> – Membantu menyemak maklumat dan kelengkapan borang bagi memastikan dokumen disediakan mengikut keperluan permohonan.</li>
</ul>
</div>
<p class="kk-tagline">Satu tempat untuk membantu anda menguruskan persediaan perkahwinan dengan lebih mudah, teratur dan yakin.</p>"""

def find(els, i, parent=None):
    for e in els:
        if e["id"] == i:
            return e, parent
        r = find(e.get("elements", []), i, e)
        if r[0]:
            return r
    return None, None

data = json.load(open(sys.argv[1]))

# heading + intro
w, _ = find(data, "1e533a5")
w["settings"]["title"] = "Perkhidmatan Kami"
w["settings"]["subtitle"] = "Kursus Praperkahwinan & Khidmat Pengurusan Perkahwinan"
w["settings"]["content"] = ("Menyediakan kemudahan kursus dan bantuan pengurusan dokumentasi perkahwinan "
                            "bagi memudahkan bakal pengantin membuat persediaan menuju alam perkahwinan.")
w["settings"]["subtitle_typography_text_transform"] = "none"

# replace old bullet list with services text-editor widget
lst, col = find(data, "5426489")
if lst:
    idx = col["elements"].index(lst)
    col["elements"][idx] = {
        "id": secrets.token_hex(4)[:7], "elType": "widget", "widgetType": "text-editor",
        "settings": {"editor": SERVICES_HTML, "_css_classes": "kk-services"},
        "elements": [], "isInner": False,
    }

# button -> WhatsApp
b, _ = find(data, "f918ead")
b["settings"]["text"] = "Hubungi Kami Hari Ini"
b["settings"]["link"] = {"url": WA, "is_external": "on", "nofollow": "", "custom_attributes": ""}
b["settings"]["selected_icon"] = {"value": "fab fa-whatsapp", "library": "fa-brands"}
b["settings"]["icon_align"] = "left"

# photo -> poster
img, _ = find(data, "52cfbbd")
img["settings"]["image"] = {"url": "https://kursuskahwinmalaysia.com/wp-content/uploads/2026/11/kkm-poster-isi-borang-kahwin.jpg",
                            "id": 1115, "size": "", "alt": "Perkhidmatan Isi Borang Kahwin RM40", "source": "library"}
img["settings"]["image_size"] = "full"

# remove the "A Cemerlang" badge from this section
badge, holder = find(data, "4b439fb")
if badge:
    holder["elements"].remove(badge)

json.dump(data, open(sys.argv[2], "w"), ensure_ascii=False)
print("ok")
