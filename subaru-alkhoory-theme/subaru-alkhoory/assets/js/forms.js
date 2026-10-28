/**
 * Subaru Al Khoory — [subaru_form] enhancements: floating labels, custom selects,
 * conditional fields, client validation and AJAX submission.
 */
(function () {
	'use strict';

	var d = document;
	var w = window;
	var cfg = w.SubaruForms || {};
	var i18n = cfg.i18n || {};
	var fine = w.matchMedia('(hover: hover) and (pointer: fine)').matches;

	function $$(s, c) { return Array.prototype.slice.call((c || d).querySelectorAll(s)); }

	var PHONE = /^\+?[0-9]{7,15}$/;
	var EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

	function fieldOf(input) { return input.closest('.sa-field'); }

	function setError(field, msg) {
		if (!field) { return; }
		var err = field.querySelector('.sa-field__error');
		var input = field.querySelector('input:not([type="hidden"]), select, textarea');
		field.classList.toggle('is-invalid', !!msg);
		field.classList.toggle('is-valid', !msg && input && input.value !== '');
		if (err) { err.textContent = msg || ''; }
		if (input) {
			if (msg) { input.setAttribute('aria-invalid', 'true'); } else { input.removeAttribute('aria-invalid'); }
		}
	}

	function validateInput(input) {
		var field = fieldOf(input);
		if (!field || field.hidden) { return ''; }
		var v = input.type === 'checkbox' ? (input.checked ? '1' : '') : String(input.value || '').trim();
		var msg = '';
		if (input.required && !v) {
			msg = input.type === 'checkbox' ? (i18n.consent || 'Please accept to continue.') : (input.getAttribute('data-required-msg') || i18n.required || 'This field is required.');
		} else if (v && input.type === 'email' && !EMAIL.test(v)) {
			msg = i18n.email || 'Please enter a valid email address.';
		} else if (v && input.type === 'tel' && !PHONE.test(v.replace(/[\s\-().]/g, ''))) {
			msg = i18n.phone || 'Please enter a valid phone number.';
		} else if (v && input.type === 'date' && input.min && v < input.min) {
			msg = i18n.date || 'Please choose a date from tomorrow onwards.';
		}
		setError(field, msg);
		return msg;
	}

	function syncFilled(field) {
		var input = field.querySelector('input:not([type="hidden"]):not([type="checkbox"]), select, textarea');
		if (input) { field.classList.toggle('is-filled', String(input.value || '') !== ''); }
	}

	/* Conditional fields: data-show-if="enquiry:sales,brochure" */
	function applyConditions(form) {
		$$('[data-show-if]', form).forEach(function (field) {
			var rule = field.getAttribute('data-show-if').split(':');
			var ctrl = form.elements[rule[0]];
			var vals = (rule[1] || '').split(',');
			var show = !!ctrl && vals.indexOf(ctrl.value) !== -1;
			field.hidden = !show;
			$$('input, select, textarea', field).forEach(function (el) { el.disabled = !show; });
		});
	}

	/* Custom select: native <select> stays the source of truth (a11y + submission). */
	function enhanceSelect(select) {
		var field = fieldOf(select);
		if (!field || field.classList.contains('sa-enhanced') || !fine) { return; }
		field.classList.add('sa-enhanced');
		select.classList.add('sa-select__native');
		select.setAttribute('tabindex', '-1');
		select.setAttribute('aria-hidden', 'true');
		var label = field.querySelector('label');
		var labelId = select.id + '-label';
		if (label) { label.id = labelId; label.removeAttribute('for'); }

		var btn = d.createElement('button');
		btn.type = 'button';
		btn.className = 'sa-select__btn';
		btn.id = select.id + '-btn';
		btn.setAttribute('aria-haspopup', 'listbox');
		btn.setAttribute('aria-expanded', 'false');
		btn.setAttribute('aria-labelledby', labelId + ' ' + btn.id);
		if (select.getAttribute('aria-describedby')) { btn.setAttribute('aria-describedby', select.getAttribute('aria-describedby')); }
		var txt = d.createElement('span');
		btn.appendChild(txt);

		var list = d.createElement('ul');
		list.className = 'sa-select__list';
		list.id = select.id + '-list';
		list.setAttribute('role', 'listbox');
		list.setAttribute('tabindex', '-1');
		list.setAttribute('aria-labelledby', labelId);
		btn.setAttribute('aria-controls', list.id);

		var opts = Array.prototype.slice.call(select.options).filter(function (o) { return o.value !== ''; });
		var lis = opts.map(function (o, i) {
			var li = d.createElement('li');
			li.className = 'sa-select__opt';
			li.id = select.id + '-opt-' + i;
			li.setAttribute('role', 'option');
			li.textContent = o.textContent;
			if (o.getAttribute('data-group')) { li.setAttribute('data-group', o.getAttribute('data-group')); }
			li.addEventListener('mousedown', function (e) { e.preventDefault(); });
			li.addEventListener('click', function () { choose(i); close(); btn.focus(); });
			list.appendChild(li);
			return li;
		});
		var active = -1;

		function render() {
			var sel = select.selectedIndex > -1 ? select.options[select.selectedIndex] : null;
			var ph = select.getAttribute('data-placeholder') || '';
			txt.textContent = sel && sel.value ? sel.textContent : ph;
			btn.classList.toggle('is-placeholder', !(sel && sel.value));
			lis.forEach(function (li, i) { li.setAttribute('aria-selected', opts[i].selected ? 'true' : 'false'); });
			syncFilled(field);
		}
		function setActive(i) {
			active = Math.max(0, Math.min(lis.length - 1, i));
			lis.forEach(function (li, k) { li.classList.toggle('is-active', k === active); });
			if (lis[active]) {
				btn.setAttribute('aria-activedescendant', lis[active].id);
				lis[active].scrollIntoView({ block: 'nearest' });
			}
		}
		function choose(i) {
			opts[i].selected = true;
			select.dispatchEvent(new Event('change', { bubbles: true }));
			render();
		}
		function open() {
			$$('.sa-field.is-open').forEach(function (f) { if (f !== field && f.__saClose) { f.__saClose(); } });
			field.classList.add('is-open');
			btn.setAttribute('aria-expanded', 'true');
			setActive(Math.max(0, opts.findIndex(function (o) { return o.selected; })));
		}
		function close() {
			field.classList.remove('is-open');
			btn.setAttribute('aria-expanded', 'false');
			btn.removeAttribute('aria-activedescendant');
		}
		field.__saClose = close;
		var typed = '';
		var typedTimer = 0;
		btn.addEventListener('click', function () { if (field.classList.contains('is-open')) { close(); } else { open(); } });
		btn.addEventListener('keydown', function (e) {
			var isOpen = field.classList.contains('is-open');
			if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
				e.preventDefault();
				if (!isOpen) { open(); return; }
				setActive(active + (e.key === 'ArrowDown' ? 1 : -1));
			} else if (e.key === 'Enter' || e.key === ' ') {
				e.preventDefault();
				if (isOpen && active > -1) { choose(active); close(); } else { open(); }
			} else if (e.key === 'Escape' || e.key === 'Tab') {
				if (isOpen) { if (e.key === 'Escape') { e.preventDefault(); } close(); }
			} else if (e.key === 'Home' && isOpen) {
				e.preventDefault(); setActive(0);
			} else if (e.key === 'End' && isOpen) {
				e.preventDefault(); setActive(lis.length - 1);
			} else if (e.key.length === 1) {
				typed += e.key.toLowerCase();
				clearTimeout(typedTimer);
				typedTimer = setTimeout(function () { typed = ''; }, 600);
				var hit = opts.findIndex(function (o) { return o.textContent.toLowerCase().indexOf(typed) === 0; });
				if (hit > -1) { if (!isOpen) { open(); } setActive(hit); }
			}
		});
		btn.addEventListener('blur', function () { setTimeout(function () { if (!field.contains(d.activeElement)) { close(); } }, 120); });
		select.addEventListener('change', render);
		field.insertBefore(btn, select.nextSibling);
		field.appendChild(list);
		render();
	}

	function showSuccess(form, data) {
		var box = d.createElement('div');
		box.className = 'sa-form__success';
		box.setAttribute('role', 'status');
		box.setAttribute('tabindex', '-1');
		box.innerHTML = '<span class="sa-form__success-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span><h3></h3><p></p>';
		box.querySelector('h3').textContent = data.title || '';
		box.querySelector('p').textContent = data.message || '';
		var h = form.offsetHeight;
		form.style.minHeight = form.classList.contains('sa-form--inline') ? '' : Math.min(h, 360) + 'px';
		form.innerHTML = '';
		form.appendChild(box);
		box.focus({ preventScroll: true });
		var r = box.getBoundingClientRect();
		if (r.top < 80 || r.top > w.innerHeight) {
			var api = w.SubaruTheme && w.SubaruTheme.api;
			if (api && api.scrollTo) { api.scrollTo(form); } else { form.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
		}
	}

	function initForm(form) {
		if (form.__saForm) { return; }
		form.__saForm = true;
		var status = form.querySelector('.sa-form__status');
		var page = form.querySelector('[name="sa_page"]');
		if (page) { page.value = w.location.href.split('#')[0]; }

		$$('.sa-field', form).forEach(syncFilled);
		// Dependent selects (e.g. grade filtered by model): options carry data-group, the select names its source.
		$$('select[data-filter-by]', form).forEach(function (sel) {
			var src = form.querySelector('[name="' + sel.getAttribute('data-filter-by') + '"]');
			if (!src) { return; }
			function apply() {
				var group = src.value;
				var hideEmpty = sel.hasAttribute('data-hide-empty');
				var visible = 0;
				Array.prototype.forEach.call(sel.options, function (o) {
					if (!o.value) { return; }
					var ok = hideEmpty ? (!!group && o.getAttribute('data-group') === group) : (!group || o.getAttribute('data-group') === group);
					o.hidden = !ok;
					o.disabled = !ok;
					if (ok) { visible++; }
				});
				var f = fieldOf(sel);
				if (hideEmpty && f) {
					// e.g. the test-drive grade: only WRX/BRZ have options; hide it for the SUVs and before a model is picked.
					f.hidden = !visible;
					sel.disabled = !visible;
				}
				if (f) { $$('.sa-select__opt[data-group]', f).forEach(function (li) { li.hidden = hideEmpty ? li.getAttribute('data-group') !== group : (!!group && li.getAttribute('data-group') !== group); }); }
				var cur = sel.options[sel.selectedIndex];
				if (cur && cur.disabled) { sel.value = ''; sel.dispatchEvent(new Event('change', { bubbles: true })); }
			}
			src.addEventListener('change', apply);
			apply();
		});
		$$('select', form).forEach(enhanceSelect);
		applyConditions(form);

		form.addEventListener('input', function (e) {
			var f = fieldOf(e.target);
			if (!f) { return; }
			syncFilled(f);
			if (f.classList.contains('is-invalid')) { validateInput(e.target); }
		});
		form.addEventListener('change', function (e) {
			var f = fieldOf(e.target);
			if (f) { syncFilled(f); if (e.target.type === 'checkbox' || e.target.tagName === 'SELECT') { validateInput(e.target); } }
			applyConditions(form);
		});
		form.addEventListener('focusout', function (e) {
			if (e.target.matches('input, textarea') && e.target.value) { validateInput(e.target); }
		});

		form.addEventListener('submit', function (e) {
			e.preventDefault();
			if (form.classList.contains('is-loading')) { return; }
			var firstBad = null;
			$$('input:not([type="hidden"]):not([name="sa_website"]), input[type="hidden"][required], select, textarea', form).forEach(function (input) {
				if (input.disabled) { return; }
				if (validateInput(input) && !firstBad) { firstBad = input; }
			});
			if (status) { status.textContent = ''; status.classList.remove('is-error'); }
			if (firstBad) {
				var f = fieldOf(firstBad);
				var focusEl = (f && f.querySelector('.sa-select__btn')) || firstBad;
				if (firstBad.type === 'hidden') {
					// e.g. the test-drive model, picked with the model cards above the form.
					focusEl = d.querySelector('.sub-model-pick') || form;
					focusEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
				}
				focusEl.focus({ preventScroll: firstBad.type === 'hidden' });
				if (status && !form.classList.contains('sa-form--inline')) { status.textContent = i18n.fix || 'Please check the highlighted fields.'; status.classList.add('is-error'); }
				return;
			}
			form.classList.add('is-loading');
			form.setAttribute('aria-busy', 'true');
			var label = form.querySelector('.sa-btn__label');
			var labelText = label ? label.textContent : '';
			if (label) { label.textContent = i18n.sending || 'Sending…'; }

			fetch(cfg.ajaxUrl || form.action, { method: 'POST', body: new FormData(form), credentials: 'same-origin' })
				.then(function (res) { return res.json().catch(function () { return { success: false, data: {} }; }); })
				.then(function (json) {
					form.classList.remove('is-loading');
					form.removeAttribute('aria-busy');
					if (label) { label.textContent = labelText; }
					var data = json && json.data ? json.data : {};
					if (json && json.success) { showSuccess(form, data); return; }
					// A reCAPTCHA response can only be used once: ask for a fresh tick after any failed attempt.
					if (w.grecaptcha && form.querySelector('.g-recaptcha')) { try { w.grecaptcha.reset(); } catch (e) { /* ignore */ } }
					if (data.errors && data.errors.captcha && !form.elements.captcha) {
						var rc = form.querySelector('.sa-field--recaptcha');
						if (rc) { setError(rc, data.errors.captcha); }
					}
					if (data.errors) {
						var first = null;
						Object.keys(data.errors).forEach(function (k) {
							var input = form.elements[k];
							if (input && input.length && !input.tagName) { input = input[0]; }
							if (input) { setError(fieldOf(input), data.errors[k]); if (!first) { first = input; } }
						});
						if (first) { var fb = fieldOf(first); ((fb && fb.querySelector('.sa-select__btn')) || first).focus(); }
					}
					if (status) { status.textContent = data.message || i18n.error; status.classList.add('is-error'); }
				})
				.catch(function () {
					form.classList.remove('is-loading');
					form.removeAttribute('aria-busy');
					if (label) { label.textContent = labelText; }
					if (status) { status.textContent = i18n.error || 'Something went wrong.'; status.classList.add('is-error'); }
				});
		});
	}

	function init(scope) { $$('form[data-sa-form]', scope || d).forEach(initForm); }

	if (d.readyState !== 'loading') { init(); } else { d.addEventListener('DOMContentLoaded', function () { init(); }); }
	d.addEventListener('click', function (e) {
		if (!e.target.closest('.sa-enhanced')) {
			$$('.sa-field.is-open').forEach(function (f) { if (f.__saClose) { f.__saClose(); } });
		}
	});
	w.SubaruFormsInit = init;
})();
