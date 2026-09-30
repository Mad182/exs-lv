<div class="rr-container">

	<!-- START BLOCK : review-list -->
	<div class="rr-header">
		<h1 class="rr-title">
			<img src="/bildes/fugue-icons/document-history.png" alt="" /> Atjaunoto rakstu pārbaude
		</h1>
		<!-- START BLOCK : start-review-btn -->
		<a href="/review-restored?review={first-pending-id}" class="rr-btn rr-btn-primary">
			<img src="/bildes/fugue-icons/arrow-skip.png" alt="" /> Sākt pārbaudi pa vienam
		</a>
		<!-- END BLOCK : start-review-btn -->
	</div>

	<!-- Stats Bar -->
	<div class="rr-stats-bar">
		<div class="rr-stat-card pending">
			<div class="rr-stat-value">{stat-pending}</div>
			<div class="rr-stat-label">Gaida pārbaudi</div>
		</div>
		<div class="rr-stat-card approved">
			<div class="rr-stat-value">{stat-approved}</div>
			<div class="rr-stat-label">Apstiprināti</div>
		</div>
		<div class="rr-stat-card rejected">
			<div class="rr-stat-value">{stat-rejected}</div>
			<div class="rr-stat-label">Noraidīti</div>
		</div>
		<div class="rr-stat-card comments">
			<div class="rr-stat-value">{stat-comments}</div>
			<div class="rr-stat-label">Atjaunoti komentāri</div>
		</div>
	</div>

	<!-- Filter Tabs & Search -->
	<div class="rr-filter-row">
		<div class="rr-tabs">
			<a href="/review-restored?status=pending" class="rr-tab {tab-pending-active}">Gaida ({stat-pending})</a>
			<a href="/review-restored?status=approved" class="rr-tab {tab-approved-active}">Apstiprināti ({stat-approved})</a>
			<a href="/review-restored?status=rejected" class="rr-tab {tab-rejected-active}">Noraidīti ({stat-rejected})</a>
			<a href="/review-restored?status=all" class="rr-tab {tab-all-active}">Visi</a>
		</div>

		<form action="/review-restored" method="GET" class="rr-search-form">
			<input type="hidden" name="status" value="{current-status}" />
			<input type="text" name="q" value="{search-q}" placeholder="Meklēt pēc virsraksta vai slug..." class="rr-search-input" />
			<button type="submit" class="rr-btn rr-btn-secondary">Meklēt</button>
		</form>
	</div>

	<!-- Articles Table -->
	<table class="rr-table">
		<thead>
			<tr>
				<th style="width: 50px;">ID</th>
				<th>Virsraksts</th>
				<th style="width: 140px;">Sadaļa</th>
				<th style="width: 120px;">Autors</th>
				<th style="width: 80px; text-align: center;" title="Kārtots pēc komentāru skaita (dilstoši)">Komentāri ▼</th>
				<th style="width: 120px;">Datums</th>
				<th style="width: 90px; text-align: center;">Statuss</th>
				<th style="width: 100px; text-align: right;">Darbības</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : review-row -->
			<tr>
				<td><strong>#{row-page-id}</strong></td>
				<td>
					<a href="/review-restored?review={row-id}" style="font-weight: 600; text-decoration: none;">
						{row-title}
					</a>
					<br /><small style="color: #64748b;">slug: /read/{row-slug}</small>
				</td>
				<td><small>{row-category}</small></td>
				<td><small>{row-author}</small></td>
				<td style="text-align: center;">
					<span class="rr-badge rr-badge-comments">{row-comments}</span>
				</td>
				<td><small>{row-date}</small></td>
				<td style="text-align: center;">
					<span class="rr-badge rr-badge-{row-status-class}">{row-status-label}</span>
				</td>
				<td style="text-align: right;">
					<a href="/review-restored?review={row-id}" class="rr-btn rr-btn-primary" style="padding: 4px 10px; font-size: 12px;">
						Pārskatīt
					</a>
				</td>
			</tr>
			<!-- END BLOCK : review-row -->

			<!-- START BLOCK : review-empty -->
			<tr>
				<td colspan="8" style="text-align: center; padding: 40px; color: #64748b;">
					Nav atrasts neviens atjaunots raksts šajā sadaļā.
				</td>
			</tr>
			<!-- END BLOCK : review-empty -->
		</tbody>
	</table>

	<!-- START BLOCK : review-pagination -->
	<div style="text-align: center; margin-top: 15px;">
		{pagination-html}
	</div>
	<!-- END BLOCK : review-pagination -->

	<!-- END BLOCK : review-list -->


	<!-- START BLOCK : review-single -->
	<!-- Top Navigation & Progress Bar -->
	<div class="rr-review-top-bar">
		<div>
			<a href="/review-restored" class="rr-btn rr-btn-outline">
				&larr; Atpakaļ uz sarakstu
			</a>
		</div>

		<div style="font-weight: 600; font-size: 14px;">
			<!-- START BLOCK : queue-position -->
			Rindā: <strong>#{pos-current}</strong> no <strong>{pos-total}</strong> gaidošajiem
			<!-- END BLOCK : queue-position -->
		</div>

		<div class="rr-nav-btns">
			<!-- START BLOCK : prev-btn -->
			<a href="/review-restored?review={prev-id}" class="rr-btn rr-btn-secondary" title="Iepriekšējais (J vai bultiņa pa kreisi)">
				&larr; Iepriekšējais
			</a>
			<!-- END BLOCK : prev-btn -->

			<a href="{wayback-url}" target="_blank" rel="noopener noreferrer" class="rr-btn rr-btn-outline" title="Atvērt arhīva snapshot jaunā cilnē">
				<img src="/bildes/fugue-icons/globe.png" alt="" /> Skatīt Archive.org
			</a>

			<!-- START BLOCK : next-btn -->
			<a href="/review-restored?review={next-id}" class="rr-btn rr-btn-secondary" title="Nākamais (K vai bultiņa pa labi)">
				Nākamais &rarr;
			</a>
			<!-- END BLOCK : next-btn -->
		</div>
	</div>

	<!-- Review & Edit Form -->
	<form action="/review-restored" method="POST" id="rr-review-form">
		<input type="hidden" name="action" id="rr-action" value="save" />
		<input type="hidden" name="review_id" value="{article-review-id}" />
		<input type="hidden" name="page_id" value="{article-page-id}" />
		<input type="hidden" name="xsrf_token" value="{xsrf-token}" />

		<div class="rr-panel">
			<div class="rr-panel-title">
				<span>Pamatinformācija</span>
				<span class="rr-badge rr-badge-{current-status-class}">{current-status-label}</span>
			</div>

			<div class="rr-form-group">
				<label class="rr-form-label" for="article_title">Virsraksts:</label>
				<input type="text" name="title" id="article_title" class="rr-form-control" value="{article-title}" required />
			</div>

			<div class="rr-grid-3">
				<div class="rr-form-group">
					<label class="rr-form-label" for="article_category">Sadaļa (Kategorija):</label>
					<select name="category" id="article_category" class="rr-form-control">
						<!-- START BLOCK : cat-group -->
						<optgroup label="{group-label}">
							<!-- START BLOCK : cat-option -->
							<option value="{cat-id}" {cat-selected}>{cat-title}</option>
							<!-- END BLOCK : cat-option -->
						</optgroup>
						<!-- END BLOCK : cat-group -->
					</select>
				</div>

				<div class="rr-form-group">
					<label class="rr-form-label">Autors:</label>
					<div class="rr-form-control" style="background: #f8fafc; display: flex; align-items: center; justify-content: space-between;">
						<span>{article-author-nick} (ID: #{article-author-id})</span>
						<a href="/user/{article-author-id}" target="_blank" style="font-size: 12px;">Profils &rarr;</a>
					</div>
				</div>

				<div class="rr-form-group">
					<label class="rr-form-label">Publicēšanas datums:</label>
					<input type="text" name="date" class="rr-form-control" value="{article-date}" />
				</div>
			</div>

			<div class="rr-grid-3" style="font-size: 13px; color: #64748b; margin-top: 5px;">
				<div>Skatījumi vēsturiski: <strong>{article-views}</strong></div>
				<div>Vērtējums: <strong>{article-rating}</strong> ({article-rating-count} balsis)</div>
				<div>Saites slug: <code>/read/{article-slug}</code></div>
			</div>
		</div>

		<!-- Article Body Content -->
		<div class="rr-panel">
			<div class="rr-panel-title">
				<span>Raksta saturs</span>
				<div class="rr-tabs-editor">
					<button type="button" class="rr-tab-btn active" id="tab-btn-preview" onclick="switchContentTab('preview')">Priekšskatījums</button>
					<button type="button" class="rr-tab-btn" id="tab-btn-source" onclick="switchContentTab('source')">Rediģēt HTML / Tekstu</button>
				</div>
			</div>

			<!-- Rendered Preview -->
			<div id="content-preview-container" class="rr-preview-box">
				{article-rendered-body}
			</div>

			<!-- Source Editor -->
			<div id="content-source-container" style="display: none;">
				<textarea name="text" id="article_source" class="rr-form-control" style="min-height: 280px; font-family: monospace; font-size: 13px; line-height: 1.5;">{article-raw-body}</textarea>
				<small style="color: #64748b; display: block; margin-top: 4px;">Varat labot HTML tagus, saites un saturu pirms publicēšanas.</small>
			</div>
		</div>

		<!-- Restored Comments -->
		<div class="rr-panel">
			<div class="rr-panel-title">
				<span>Saglabātie komentāri ({article-comments-count})</span>
			</div>

			<!-- START BLOCK : comments-empty -->
			<p style="color: #64748b; font-style: italic; margin: 10px 0;">Šim rakstam nav atrasts neviens saglabāts komentārs.</p>
			<!-- END BLOCK : comments-empty -->

			<div class="rr-comments-list">
				<!-- START BLOCK : review-comment -->
				<div class="rr-comment-item {comment-reply-class}">
					<div class="rr-comment-meta">
						<div>
							<strong>{comment-author}</strong>
							<span style="color: #94a3b8; margin: 0 4px;">&bull;</span>
							<span>{comment-date}</span>
							<span style="color: #94a3b8; margin: 0 4px;">&bull;</span>
							<small>#{comment-id}</small>
						</div>
						<div>
							<span class="rr-badge" style="background: #e2e8f0; font-size: 11px;">Vērtējums: {comment-vote}</span>
						</div>
					</div>
					<div class="rr-comment-text">
						{comment-text}
					</div>
				</div>
				<!-- END BLOCK : review-comment -->
			</div>
		</div>

		<!-- Sticky Review Action Bar -->
		<div class="rr-action-bar">
			<div class="rr-keyboard-hints">
				<span>Īsinājumtaustiņi:</span>
				<span><span class="rr-key">A</span> Apstiprināt</span>
				<span><span class="rr-key">R</span> Noraidīt</span>
				<span><span class="rr-key">&larr;</span> Iepriekšējais</span>
				<span><span class="rr-key">&rarr;</span> Nākamais</span>
			</div>

			<div style="display: flex; gap: 10px; align-items: center;">
				<!-- Reject / Delete Button -->
				<button type="button" class="rr-btn rr-btn-danger" onclick="submitReviewAction('reject')">
					<img src="/bildes/fugue-icons/cross-circle.png" alt="" /> Noraidīt / Dzēst
				</button>

				<!-- Save Changes Button -->
				<button type="button" class="rr-btn rr-btn-secondary" onclick="submitReviewAction('save')">
					<img src="/bildes/fugue-icons/disk.png" alt="" /> Saglabāt izmaiņas
				</button>

				<!-- Approve and Publish Button -->
				<button type="button" class="rr-btn rr-btn-success" onclick="submitReviewAction('approve')">
					<img src="/bildes/fugue-icons/tick-circle.png" alt="" /> Apstiprināt un publicēt &rarr;
				</button>
			</div>
		</div>
	</form>

	<script>
		function switchContentTab(mode) {
			const previewContainer = document.getElementById('content-preview-container');
			const sourceContainer = document.getElementById('content-source-container');
			const btnPreview = document.getElementById('tab-btn-preview');
			const btnSource = document.getElementById('tab-btn-source');

			if (mode === 'preview') {
				// Update preview with whatever is in source textarea
				previewContainer.innerHTML = document.getElementById('article_source').value;
				previewContainer.style.display = 'block';
				sourceContainer.style.display = 'none';
				btnPreview.classList.add('active');
				btnSource.classList.remove('active');
			} else {
				previewContainer.style.display = 'none';
				sourceContainer.style.display = 'block';
				btnPreview.classList.remove('active');
				btnSource.classList.add('active');
			}
		}

		function submitReviewAction(act) {
			if (act === 'reject') {
				if (!confirm('Vai tiešām vēlies noraidīt un dzēst šo rakstu?')) {
					return;
				}
			}
			document.getElementById('rr-action').value = act;
			document.getElementById('rr-review-form').submit();
		}

		// Keyboard Shortcuts Listener
		document.addEventListener('keydown', function(e) {
			// Do not trigger if typing in input, textarea, or select
			const tag = e.target.tagName.toLowerCase();
			if (tag === 'input' || tag === 'textarea' || tag === 'select') {
				return;
			}

			if (e.key === 'a' || e.key === 'A') {
				e.preventDefault();
				submitReviewAction('approve');
			} else if (e.key === 'r' || e.key === 'R') {
				e.preventDefault();
				submitReviewAction('reject');
			} else if (e.key === 'ArrowRight' || e.key === 'k') {
				<!-- START BLOCK : next-link-js -->
				window.location.href = '/review-restored?review={next-id}';
				<!-- END BLOCK : next-link-js -->
			} else if (e.key === 'ArrowLeft' || e.key === 'j') {
				<!-- START BLOCK : prev-link-js -->
				window.location.href = '/review-restored?review={prev-id}';
				<!-- END BLOCK : prev-link-js -->
			}
		});
	</script>
	<!-- END BLOCK : review-single -->

</div>
