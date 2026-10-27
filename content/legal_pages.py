# Content for the FIERRO "Privacy & Cookie Policy" and "Terms & Conditions" pages.
# Each page: (page title, slug, [(section heading or None, paragraph html), ...])
# Paragraph html is placed in an Elementor Heading widget (<p>), so use <br> and <a>, not lists.

SITE = "https://soharon.co.uk/dev-101"
EMAIL = "support@fierrojewelry.com"
PHONE = "+971 52 779 4759"
UPDATED = "27 October 2026"

def link(path, text):
    return f'<a href="{SITE}{path}" style="color:#222222;text-decoration:underline;">{text}</a>'

MAIL = f'<a href="mailto:{EMAIL}" style="color:#222222;text-decoration:underline;">{EMAIL}</a>'

PRIVACY = ("PRIVACY & COOKIE POLICY", "privacy-cookie-policy", [
    (None,
     f"Last updated: {UPDATED}<br><br>"
     "This website is operated by FIERRO (\"FIERRO\", \"we\", \"us\"), P.O. Box 125074, Dubai, United Arab Emirates. "
     "We respect your privacy. This policy explains what personal data we collect when you visit our website, create an account "
     "or place an order, how we use it and the choices you have. We handle personal data in line with the laws of the United Arab "
     "Emirates, including Federal Decree-Law No. 45 of 2021 on the Protection of Personal Data."),
    ("INFORMATION WE COLLECT",
     "<strong>Information you give us:</strong> your name, email address, phone number, billing and delivery address, order notes, "
     "account login details, and anything you send us through our contact, distributor/retailer or newsletter forms.<br><br>"
     "<strong>Order and payment information:</strong> the products you buy, order value, currency and delivery details. Card payments "
     "are processed securely by our payment provider (Stripe) and instalment payments by Tabby. We never see or store your full card number.<br><br>"
     "<strong>Technical information:</strong> your IP address, device and browser type, the pages you visit, and your chosen currency "
     "and wishlist items, collected through cookies (see below)."),
    ("HOW WE USE YOUR INFORMATION",
     "We use your information to:<br>"
     "• process, deliver and support your orders, returns and exchanges<br>"
     "• take payment and prevent fraud<br>"
     "• manage your account and wishlist<br>"
     "• answer your questions and requests<br>"
     "• send you our newsletter and offers, only if you have subscribed (you can unsubscribe at any time)<br>"
     "• keep our website secure and improve it<br>"
     "• meet our legal, accounting and tax obligations"),
    ("WHO WE SHARE IT WITH",
     "We only share your information where needed to run our business:<br>"
     "• <strong>Payment providers</strong> (Stripe, and Tabby if you choose to pay in instalments) to process payments<br>"
     "• <strong>Delivery partners and couriers</strong> to deliver your order<br>"
     "• <strong>Website, hosting and email service providers</strong> who help us run this website<br>"
     "• <strong>Authorities</strong> when required by law<br><br>"
     "We do not sell or rent your personal data. Some of these providers may process data outside the UAE; where this happens we "
     "rely on them to protect it to an appropriate standard."),
    ("HOW LONG WE KEEP IT",
     "We keep order and invoice records for as long as UAE law requires (generally at least five years for tax records). "
     "Account information is kept while your account is active. Newsletter data is kept until you unsubscribe. "
     "Other information is deleted when it is no longer needed."),
    ("KEEPING YOUR DATA SAFE",
     "Our website uses secure (HTTPS) connections, payments are handled by PCI-DSS compliant providers, and access to personal "
     "data is limited to people who need it. No method of transmission over the internet is completely secure, but we take "
     "reasonable steps to protect your information."),
    ("YOUR RIGHTS",
     f"You can ask us to access, correct or delete your personal data, to stop sending you marketing, or to withdraw a consent you "
     f"have given. To make a request, email {MAIL}. We will reply within 30 days. You can also update your details at any time in "
     f"{link('/my-account/', 'My Account')}."),
    ("CHILDREN",
     "Our website is intended for adults. We do not knowingly collect personal data from anyone under 18."),
    ("COOKIES",
     "Cookies are small text files stored on your device when you visit a website. We use them for the following purposes:<br><br>"
     "<strong>Essential cookies</strong> make the website work: your shopping cart, checkout, account login, security and fraud "
     "prevention during payment. The website cannot work properly without them.<br><br>"
     "<strong>Preference cookies</strong> remember your choices, such as the currency you selected and the items in your wishlist.<br><br>"
     "We do not currently use advertising cookies or third-party analytics cookies. If we add them in the future, we will update "
     "this policy and ask for your consent where required.<br><br>"
     "You can block or delete cookies in your browser settings. If you block essential cookies, the cart and checkout may not work."),
    ("CHANGES TO THIS POLICY",
     "We may update this policy from time to time. The latest version will always be on this page with the date it was last updated."),
    ("CONTACT US",
     f"If you have any questions about this policy or your personal data, contact us:<br>"
     f"Email: {MAIL}<br>Phone / WhatsApp: {PHONE}<br>Address: FIERRO, P.O. Box 125074, Dubai, United Arab Emirates"),
])

TERMS = ("TERMS & CONDITIONS", "terms-conditions", [
    (None,
     f"Last updated: {UPDATED}<br><br>"
     "These Terms & Conditions apply to your use of this website and to all orders placed on it. The website is operated by "
     "FIERRO, P.O. Box 125074, Dubai, United Arab Emirates. By using the website or placing an order you agree to these terms, so "
     "please read them carefully."),
    ("YOUR ACCOUNT",
     "You must be at least 18 years old to place an order. Please make sure the information you give us is accurate and keep your "
     "password confidential. You are responsible for activity on your account."),
    ("OUR PRODUCTS",
     f"FIERRO offers fashion jewelry, watches and accessories. We make every effort to show our products accurately, but colours "
     f"and finishes may look slightly different on your screen, and product images are for illustration. Plated jewelry needs "
     f"proper care; please follow our {link('/jewelry-care/', 'Jewelry Care')} guide."),
    ("PRICES & CURRENCY",
     "Prices are in UAE Dirham (AED) and are inclusive of all taxes, including VAT. Prices shown in other currencies are for your "
     "convenience and are converted using daily exchange rates; you are charged in the currency shown at checkout, and your bank "
     "may apply its own conversion fees. Prices and availability can change without notice. If a product is listed at an incorrect "
     "price, we may cancel the order and refund you in full."),
    ("ORDERS",
     "After you place an order you will receive an email confirming that we have received it. Your order is accepted when it is "
     "dispatched. We may decline or cancel an order, for example if a product is out of stock, the price is incorrect or we suspect "
     "fraud. If we cancel after you have paid, we will refund you in full."),
    ("PAYMENT",
     "We accept credit and debit cards (Visa, Mastercard, American Express) and, where available, Apple Pay, Google Pay and Link. "
     "Payments are processed securely by Stripe. You may also be offered instalment payments with Tabby, subject to Tabby's own "
     "terms and approval. We do not store your card details."),
    ("SHIPPING & DELIVERY",
     f"<strong>UAE:</strong> orders placed before 1 PM are delivered the same day; orders after 1 PM are delivered the next working "
     f"day. Orders placed on Saturday after 1 PM are delivered on Monday. There are no deliveries on weekends or public holidays. "
     f"Delivery is free on orders above AED 150; a flat rate of AED 25 applies below that.<br><br>"
     f"<strong>International:</strong> delivery usually takes 3 to 7 working days. The shipping cost is calculated at checkout. "
     f"Customs duties and import taxes are the responsibility of the recipient. We are not responsible for delays caused by customs "
     f"or the courier. If a delivery is refused, the customer is responsible for the charges incurred, including return shipping.<br><br>"
     f"More details: {link('/shipping-and-payments/', 'Shipping & Payments')}."),
    ("CANCELLING OR CHANGING AN ORDER",
     f"You can cancel or change your order before it is dispatched by contacting us at {MAIL} or on WhatsApp at {PHONE}. "
     f"Once an order has been dispatched it can no longer be cancelled."),
    ("RETURNS, EXCHANGES & REFUNDS",
     f"If you receive a faulty or incorrect item, contact us within 24 hours of delivery. Items can be returned in their original, "
     f"unused condition with packaging and proof of purchase. Items bought on sale or with a discount, accidentally damaged items "
     f"and earrings (for hygiene reasons) cannot be returned unless faulty. Returns and exchanges are available for UAE orders "
     f"only, and shipping charges are not refundable. Approved refunds are made to the original payment method. "
     f"Full details: {link('/returns-exchange/', 'Returns & Exchange')}."),
    ("WARRANTY",
     f"Our products are covered by the warranty described on our {link('/warranty/', 'Warranty')} page. Complimentary gifts are not "
     f"covered by the warranty and cannot be returned or exchanged."),
    ("OFFERS & COUPON CODES",
     "Coupon codes and promotions are subject to their stated conditions, cannot be exchanged for cash and may be changed or "
     "withdrawn at any time. Unless stated otherwise, only one coupon code can be used per order."),
    ("INTELLECTUAL PROPERTY",
     "All content on this website, including the FIERRO name and logo, product designs, photographs, videos and text, belongs to "
     "FIERRO or its licensors. You may not copy, download, reproduce or use it without our written permission."),
    ("USING OUR WEBSITE",
     "You agree not to misuse the website, including attempting to access it without authorisation, interfering with its "
     "operation or using it for any unlawful purpose."),
    ("OUR LIABILITY",
     "To the extent permitted by law, FIERRO is not liable for indirect or consequential loss, and our total liability for any "
     "order is limited to the amount you paid for it. Nothing in these terms limits your rights under UAE Federal Law No. 15 of "
     "2020 on Consumer Protection."),
    ("PRIVACY",
     f"Our {link('/privacy-cookie-policy/', 'Privacy & Cookie Policy')} explains how we use your personal data."),
    ("GOVERNING LAW",
     "These terms are governed by the laws of the United Arab Emirates as applied in the Emirate of Dubai, and the courts of Dubai "
     "have jurisdiction over any dispute."),
    ("CHANGES TO THESE TERMS",
     "We may update these terms from time to time. The version on this page at the time you place an order applies to that order."),
    ("CONTACT US",
     f"Email: {MAIL}<br>Phone / WhatsApp: {PHONE}<br>Address: FIERRO, P.O. Box 125074, Dubai, United Arab Emirates"),
])
