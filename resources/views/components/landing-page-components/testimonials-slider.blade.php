@props([
'class' => '',
'title' => 'What our customers say',
'subtitle' => 'Real stories from customers who trust Fin-noys for cleaner, calmer spaces.',
'rows' => null,
])

@php
	$formatDisplayName = function ($name) {
		$parts = collect(preg_split('/\s+/', trim((string) $name)) ?: [])->filter()->values();
		if ($parts->isEmpty()) {
			return 'Anonymous Client';
		}
		$first = $parts->first();
		$last = $parts->count() > 1 ? $parts->last() : '';
		if ($last === '') {
			return $first;
		}
		return $first . ' ' . strtoupper(mb_substr($last, 0, 1)) . '.';
	};

	$mapFeedbackToCard = function ($feedback) use ($formatDisplayName) {
		$rawName = data_get($feedback, 'client.full_name') ?: data_get($feedback, 'client.name') ?: 'Anonymous Client';
		$name = $formatDisplayName($rawName);
$comment = trim((string) ($feedback->comments ?: $feedback->feedback_text ?: ''));
$serviceType = trim((string) ($feedback->service_type ?: ''));
$rating = (int) ($feedback->overall_rating ?: $feedback->rating ?: 5);
		$initials = collect(preg_split('/\s+/', trim($rawName)) ?: [])
->filter()
->take(2)
->map(function ($part) {
return strtoupper(mb_substr($part, 0, 1));
})
->implode('');

return [
'name' => $name,
'role' => $serviceType ?: 'Client Feedback',
'quote' => $comment !== '' ? $comment : 'Thank you for the positive feedback.',
'rating' => max(1, min(5, $rating)),
'service_type' => $serviceType,
'created_at' => optional($feedback->created_at)->format('M d, Y'),
'avatar' => $initials !== '' ? $initials : 'C',
'would_recommend' => (bool) ($feedback->would_recommend ?? false),
];
};

$defaultRows = [
[
[
'name' => 'Elina Saarinen',
'role' => 'Hotel Manager',
'quote' => 'Fin-noys transformed our turnaround routine. Rooms are guest-ready faster and consistently spotless.',
'rating' => 5,
],
[
'name' => 'Mika Korhonen',
'role' => 'Restaurant Owner',
'quote' => 'Their team is precise, polite, and always on time. It feels like they are part of our own staff.',
'rating' => 5,
],
[
'name' => 'Aino Lehtinen',
'role' => 'Property Host',
'quote' => 'Peak season used to be chaotic. Now every handover is smooth, with zero last-minute stress.',
'rating' => 5,
],
[
'name' => 'Riku Miettinen',
'role' => 'Operations Lead',
'quote' => 'Communication is excellent and reports are clear. We always know what is done and when.',
'rating' => 5,
],
],
[
[
'name' => 'Sofia Niemi',
'role' => 'Airbnb Superhost',
'quote' => 'Guests keep praising the cleanliness. Ratings climbed almost immediately after switching to Fin-noys.',
'rating' => 5,
],
[
'name' => 'Janne Virtanen',
'role' => 'Facility Coordinator',
'quote' => 'The scheduling is reliable and flexible. Even urgent requests are handled with confidence.',
'rating' => 5,
],
[
'name' => 'Noora Kallio',
'role' => 'Wellness Studio Owner',
'quote' => 'Our studio feels fresher every day. Members noticed the difference before we even announced anything.',
'rating' => 5,
],
[
'name' => 'Tuomas Salmi',
'role' => 'Office Administrator',
'quote' => 'Dependable, discreet, and detail-focused. The quality stays high week after week.',
'rating' => 5,
],
],
];

$feedbackRows = [];
$clientFeedbackCount = 0;
$clientFeedbackAverage = 0;
try {
$clientFeedbackQuery = \App\Models\Feedback::where('user_type', 'client');
$clientFeedbackCount = (clone $clientFeedbackQuery)->count();
$clientFeedbackAverage = (float) ((clone $clientFeedbackQuery)->avg('overall_rating') ?? 0);
$feedbackRows = \App\Models\Feedback::with('client')
->latest()
->take(12)
->get()
->map($mapFeedbackToCard)
->values()
->all();
} catch (\Throwable $e) {
$feedbackRows = [];
$clientFeedbackAverage = 0;
}
$displayAverage = number_format(max(0, min(5, $clientFeedbackAverage)), 1);

if (is_array($rows) && count($rows)) {
$rowsData = array_values(array_slice($rows, 0, 2));
} elseif (count($feedbackRows)) {
$rowsData = array_chunk($feedbackRows, max(1, (int) ceil(count($feedbackRows) / 2)));
} else {
$rowsData = $defaultRows;
}
$componentId = 'tdst-' . uniqid();
$velocityByRow = [5.5, 4.8];
$directionByRow = [1, -1];
$velocityJson = json_encode($velocityByRow);
$directionJson = json_encode($directionByRow);
$safeTitle = strip_tags((string) $title);
$styledTitle = preg_replace('/(customers)/i', '<span class="aurora-text">$1</span>', $safeTitle, 1);
@endphp

@once
@push('styles')
<style>
	.tdst-shell {
		position: relative;
		isolation: isolate;
	}

	.tdst-row {
		width: 100%;
		overflow: hidden;
		white-space: nowrap;
		padding: 0.625rem 0;
	}

	.tdst-track {
		display: inline-flex;
		will-change: transform;
		transform: translate3d(0, 0, 0);
		backface-visibility: hidden;
	}

	.tdst-copy {
		display: inline-flex;
		flex-shrink: 0;
	}

	.tdst-block {
		display: inline-flex;
		flex-shrink: 0;
		gap: 1rem;
		padding-right: 1rem;
	}

	.tdst-card {
		width: min(90vw, 21rem);
		border-radius: 1rem;
		border: 1px solid rgba(148, 163, 184, 0.3);
		background: rgba(255, 255, 255, 0.78);
		backdrop-filter: blur(10px);
		-webkit-backdrop-filter: blur(10px);
		box-shadow: 0 16px 30px -22px rgba(15, 23, 42, 0.35);
		padding: 1rem;
		display: flex;
		flex-direction: column;
		gap: 0.75rem;
		white-space: normal;
	}

	.dark .tdst-card {
		border-color: rgba(71, 85, 105, 0.45);
		background: rgba(17, 24, 39, 0.76);
		box-shadow: 0 20px 36px -24px rgba(0, 0, 0, 0.55);
	}

	.tdst-stars {
		letter-spacing: 0.2em;
		font-size: 0.85rem;
		color: rgb(250 204 21);
	}

	.tdst-quote {
		color: rgb(30 41 59);
		font-size: 0.95rem;
		line-height: 1.45;
	}

	.dark .tdst-quote {
		color: rgb(226 232 240);
	}

	.tdst-author {
		display: flex;
		flex-direction: column;
		gap: 0.1rem;
	}

	.tdst-author-name {
		color: rgb(15 23 42);
		font-size: 0.9rem;
		font-weight: 700;
	}

	.dark .tdst-author-name {
		color: rgb(248 250 252);
	}

	.tdst-author-role {
		color: rgb(71 85 105);
		font-size: 0.78rem;
	}

	.dark .tdst-author-role {
		color: rgb(148 163 184);
	}

	@media (max-width: 767px) {
		.tdst-row {
			padding: 0.45rem 0;
		}

		.tdst-card {
			padding: 0.9rem;
		}

		.tdst-quote {
			font-size: 0.875rem;
		}
	}
</style>
@endpush
@endonce

<section id="{{ $componentId }}" class="tdst-shell scroll-zoom {{ $class }} w-full pb-32">

	<div class="mx-auto w-full px-4 sm:px-6 md:px-8">
		<div class="mx-auto max-w-3xl text-center my-3 scroll-zoom-child">
			<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-quote-icon lucide-quote mx-auto mb-4 text-blue-500 dark:text-blue-400">
				<path d="M16 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z" />
				<path d="M5 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z" />
			</svg>
			<h3 class="text-6xl font-bold text-slate-900 dark:text-slate-100">
				{!! $styledTitle !!}
			</h3>
			<p class="my-6 text-sm sm:text-base text-slate-600 dark:text-slate-300">
				{{ $subtitle }}
				<a href="{{ route('login') }}" class="ml-1 inline-flex items-center gap-1 font-semibold text-blue-600 underline decoration-blue-400/70 underline-offset-4 transition-colors hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
					Want to add a feedback?
				</a>
			</p>
			<div class="mt-4 inline-flex items-center gap-3 sm:gap-4 rounded-full border border-slate-200 bg-white/80 px-4 text-slate-700 shadow-sm backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200">
				<span class="text-sm sm:text-base font-semibold tracking-tight">{{ $displayAverage }}/5</span>
				<span class="inline-flex items-center">
					<img src="{{ asset('images/finnoys-text-logo.svg') }}" alt="Fin-noys Logo" class="h-12 w-auto hidden dark:block">
					<img src="{{ asset('images/finnoys-text-logo-light.svg') }}" alt="Fin-noys Logo" class="h-12 w-auto block dark:hidden">
				</span>
				<span class="hidden sm:inline text-sm text-slate-600 dark:text-slate-300">Based on {{ number_format($clientFeedbackCount) }} reviews</span>
				<span class="sm:hidden text-sm text-slate-600 dark:text-slate-300">{{ number_format($clientFeedbackCount) }} reviews</span>
			</div>
		</div>
		<!-- FEEDBACK MODAL -->
		<div class="space-y-0.5 scroll-zoom-child" data-tdst-container></div>

		<div data-tdst-modal class="fixed inset-0 z-[9999] hidden items-center justify-center p-4 bg-black/50 dark:bg-black/70">
			<div data-tdst-close class="absolute inset-0"></div>
			<div class="relative py-6 bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-100 dark:border-gray-800 overflow-hidden">
				<button type="button" data-tdst-close
					class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center bg-gray-900 dark:bg-gray-800 text-white rounded-full hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:focus:ring-gray-700 z-10">
					<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
						<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
					</svg>
				</button>
				<div class="max-h-[90vh] overflow-y-auto p-8">
					<div class="text-center flex flex-col gap-1 my-3.5">
						<p class="text-xs text-gray-500 dark:text-gray-400 tracking-wide">Your feedback matters</p>
						<h3 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white leading-tight my-4">How would you rate<br class="hidden sm:block">this service?</h3>
						<p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed max-w-sm mx-auto">Your input is valuable in helping us better understand your needs.</p>
					</div>

					<form data-tdst-feedback-form>
						<div class="mb-6">
							<label class="mb-3 block text-sm font-semibold text-gray-700 dark:text-gray-300">Which service did you use?</label>
							<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
								<label class="relative cursor-pointer">
									<input type="radio" name="service_type" value="Final Cleaning" checked class="peer sr-only">
									<div class="rounded-lg border-2 border-gray-300 p-4 transition-all hover:border-blue-400 peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:border-gray-600 dark:hover:border-blue-400 dark:peer-checked:bg-blue-900/20">
										<div class="flex items-center gap-3">
											<div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
												<i class="fa-solid fa-broom text-blue-600 dark:text-blue-400"></i>
											</div>
											<div>
												<div class="font-semibold text-gray-900 dark:text-white">Final Cleaning</div>
												<div class="text-xs text-gray-500 dark:text-gray-400">Complete cleaning service</div>
											</div>
										</div>
									</div>
								</label>

								<label class="relative cursor-pointer">
									<input type="radio" name="service_type" value="Deep Cleaning" class="peer sr-only">
									<div class="rounded-lg border-2 border-gray-300 p-4 transition-all hover:border-purple-400 peer-checked:border-purple-600 peer-checked:bg-purple-50 dark:border-gray-600 dark:hover:border-purple-400 dark:peer-checked:bg-purple-900/20">
										<div class="flex items-center gap-3">
											<div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
												<i class="fa-solid fa-sparkles text-purple-600 dark:text-purple-400"></i>
											</div>
											<div>
												<div class="font-semibold text-gray-900 dark:text-white">Deep Cleaning</div>
												<div class="text-xs text-gray-500 dark:text-gray-400">Intensive cleaning service</div>
											</div>
										</div>
									</div>
								</label>
							</div>
						</div>

						<div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
							<div>
								<label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Your Name</label>
								<input name="name" type="text" required placeholder="Enter your name"
									class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
							</div>
							<div>
								<label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Role / Company</label>
								<input name="role" type="text" required placeholder="e.g. Client, Manager, Host"
									class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
							</div>
						</div>

						<div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
							<label class="mb-3 block text-sm font-semibold text-gray-700 dark:text-gray-300">Overall Experience</label>
							<div class="flex items-center gap-2" data-tdst-rating>
								<input type="hidden" name="overall_rating" value="0">
								@for ($i = 1; $i <= 5; $i++)
									<button type="button" data-tdst-star="{{ $i }}" class="transition-transform hover:scale-110 focus:outline-none">
									<svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300 dark:text-gray-600 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
										<path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
									</svg>
									</button>
									@endfor
									<span class="ml-3 text-lg font-semibold text-gray-700 dark:text-gray-300" data-tdst-rating-label hidden></span>
							</div>
						</div>

						<div class="mb-4">
							<label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Your Comments</label>
							<textarea name="comments" rows="2" required minlength="10" placeholder="Add a comment"
								class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
							<p class="mt-2 text-xs text-gray-500 dark:text-gray-400"><i class="fa-solid fa-circle-info mr-1"></i>Your feedback helps us improve our services</p>
						</div>

						<div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
							<label class="flex cursor-pointer items-center">
								<input type="checkbox" name="would_recommend" checked class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
								<span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300"><i class="fa-solid fa-thumbs-up mr-2"></i>I would recommend this service to others</span>
							</label>
						</div>

						<div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
							<button type="button" data-tdst-reset-form class="rounded-lg bg-gray-200 px-6 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
								<i class="fa-solid fa-rotate-right mr-2"></i>Reset Form
							</button>
							<button type="submit" class="rounded-full bg-blue-900 dark:bg-blue-700 px-8 py-3 text-sm font-bold text-white transition-colors hover:bg-blue-800 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900 dark:focus:ring-blue-700">
								<i class="fa-solid fa-paper-plane mr-2"></i>Add Testimonial
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<script type="application/json" data-tdst-rows-json>{!! json_encode($rowsData) !!}</script>
		<script type="application/json" data-tdst-velocities-json>{!! json_encode($velocityByRow) !!}</script>
		<script type="application/json" data-tdst-directions-json>{!! json_encode($directionByRow) !!}</script>
	</div>
</section>

@once
@push('scripts')
<script>
	(function() {
		const clamp = (min, max, value) => Math.min(max, Math.max(min, value));

		const escapeHtml = (value) => String(value ?? '')
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');

		const formatDisplayName = (value) => {
			const name = String(value ?? '').trim();
			if (!name) return 'Anonymous Client';
			const parts = name.split(/\s+/).filter(Boolean);
			if (parts.length === 1) return parts[0];
			const first = parts[0];
			const last = parts[parts.length - 1];
			return `${first} ${last.charAt(0).toUpperCase()}.`;
		};

		const initThreeDScrollTrigger = (root) => {
			if (!root) return;

			const container = root.querySelector('[data-tdst-container]');
			const openButton = root.querySelector('[data-tdst-open-modal]');
			const modal = root.querySelector('[data-tdst-modal]');
			const form = root.querySelector('[data-tdst-feedback-form]');
			const resetButton = root.querySelector('[data-tdst-reset-form]');
			const ratingInput = form ? form.querySelector('input[name="overall_rating"]') : null;
			const ratingLabel = form ? form.querySelector('[data-tdst-rating-label]') : null;
			const starButtons = form ? Array.from(form.querySelectorAll('[data-tdst-star]')) : [];
			const parseJsonFromNode = (selector, fallback) => {
				const node = root.querySelector(selector);
				if (!node) return fallback;
				try {
					return JSON.parse(node.textContent || 'null') ?? fallback;
				} catch (error) {
					return fallback;
				}
			};

			const initialRows = parseJsonFromNode('[data-tdst-rows-json]', []);
			const rowVelocities = parseJsonFromNode('[data-tdst-velocities-json]', [5.5, 4.8]);
			const rowDirections = parseJsonFromNode('[data-tdst-directions-json]', [1, -1]);

			if (modal && modal.parentElement !== document.body) {
				document.body.appendChild(modal);
			}

			const state = {
				rows: Array.isArray(initialRows) ?
					initialRows.map((row) => Array.isArray(row) ? row.map((item) => ({
						...item
					})) : []) :
					[],
				rowStates: [],
				visibilityObserver: null,
				resizeObserver: null,
				previousFrameTime: null,
				previousScrollY: window.scrollY,
				smoothVelocity: 0,
				animationFrameId: null,
			};

			const showModal = () => {
				if (!modal) return;
				modal.classList.remove('hidden');
				modal.classList.add('flex');
				modal.setAttribute('aria-hidden', 'false');
				document.body.classList.add('overflow-hidden');
				window.setTimeout(() => {
					const firstField = form ? form.querySelector('input[name="name"]') : null;
					if (firstField) firstField.focus();
				}, 50);
			};

			const hideModal = () => {
				if (!modal) return;
				modal.classList.add('hidden');
				modal.classList.remove('flex');
				modal.setAttribute('aria-hidden', 'true');
				document.body.classList.remove('overflow-hidden');
			};

			const setRating = (rating) => {
				if (ratingInput) ratingInput.value = String(rating);
				if (ratingLabel) {
					ratingLabel.hidden = rating <= 0;
					ratingLabel.textContent = rating > 0 ? `${rating} / 5` : '';
				}
				starButtons.forEach((button) => {
					const value = Number(button.dataset.tdstStar || '0');
					const icon = button.querySelector('svg');
					const active = value > 0 && value <= rating;
					if (icon) {
						icon.setAttribute('fill', active ? 'currentColor' : 'none');
						icon.classList.toggle('text-yellow-500', active);
						icon.classList.toggle('text-gray-300', !active);
						icon.classList.toggle('dark:text-gray-600', !active);
					}
				});
			};

			const renderCard = (item) => {
				const rating = Math.max(1, Math.min(5, Number(item.rating ?? 5)));
				const stars = '★'.repeat(rating);
				const serviceType = String(item.service_type ?? '').trim();
				const displayName = formatDisplayName(item.name ?? '');
				const roleText = [item.role ?? '', item.created_at ? item.created_at : ''].filter(Boolean).join(' · ');

				return `
							<article class="tdst-card">
								<div class="flex items-center gap-3">
									<div class="flex items-center gap-3">
										<div class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-cyan-400 text-sm font-bold text-white shadow-md shadow-blue-500/20">
											${escapeHtml(item.avatar ?? 'C')}
										</div>
										<div class="min-w-0">
											<div class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100">${escapeHtml(displayName)}</div>
											<div class="text-xs text-slate-500 dark:text-slate-400">${escapeHtml(roleText)}</div>
										</div>
									</div>
								</div>
								<div class="tdst-stars" aria-hidden="true">${stars}</div>
								<p class="tdst-quote">“${escapeHtml(item.quote ?? '')}”</p>
								<div class="flex items-end justify-between gap-3">
									<div class="tdst-author">
										<span class="tdst-author-name">${escapeHtml(item.service_type ?? 'Client Feedback')}</span>
									</div>
								</div>
							</article>
						`;
			};

			const rebuildRowStates = () => {
				if (state.visibilityObserver) state.visibilityObserver.disconnect();
				if (state.resizeObserver) state.resizeObserver.disconnect();

				state.rowStates = Array.from(container.querySelectorAll('[data-tdst-row]')).map((row) => {
					const track = row.querySelector('.tdst-track');
					const initialBlock = row.querySelector('.tdst-block');
					if (!track || !initialBlock) return null;

					const template = initialBlock.cloneNode(true);
					track.innerHTML = '';

					const rowState = {
						row,
						track,
						template,
						unitWidth: 0,
						x: 0,
						inView: true,
						baseVelocity: Number(row.dataset.baseVelocity || '5'),
						direction: Number(row.dataset.direction || '1') >= 0 ? 1 : -1,
					};

					rowState.rebuildCopies = () => {
						rowState.track.innerHTML = '';

						const first = rowState.template.cloneNode(true);
						rowState.track.appendChild(first);

						rowState.unitWidth = first.scrollWidth;
						if (!rowState.unitWidth) return;

						const visibleWidth = row.offsetWidth || container.offsetWidth || window.innerWidth;
						const totalCopies = Math.max(3, Math.ceil(visibleWidth / rowState.unitWidth) + 2);

						for (let index = 1; index < totalCopies; index += 1) {
							rowState.track.appendChild(rowState.template.cloneNode(true));
						}

						rowState.x = rowState.unitWidth ? (rowState.x % rowState.unitWidth) : 0;
						rowState.track.style.transform = `translate3d(${-rowState.x}px,0,0)`;
					};

					rowState.rebuildCopies();
					return rowState;
				}).filter(Boolean);

				if (!state.rowStates.length) return;

				state.visibilityObserver = new IntersectionObserver((entries) => {
					entries.forEach((entry) => {
						const rowState = state.rowStates.find((current) => current.row === entry.target);
						if (rowState) {
							rowState.inView = entry.isIntersecting;
						}
					});
				}, {
					root: null,
					rootMargin: '20% 0px',
					threshold: 0,
				});

				state.rowStates.forEach((rowState) => state.visibilityObserver.observe(rowState.row));

				state.resizeObserver = new ResizeObserver(() => {
					state.rowStates.forEach((rowState) => rowState.rebuildCopies());
				});

				state.resizeObserver.observe(container);
			};

			const renderTestimonials = () => {
				container.innerHTML = state.rows.map((rowItems, rowIndex) => {
					const baseVelocity = rowVelocities[rowIndex % rowVelocities.length];
					const direction = rowDirections[rowIndex % rowDirections.length];

					return `
								<div class="tdst-row" data-tdst-row data-base-velocity="${baseVelocity}" data-direction="${direction}">
									<div class="tdst-track">
										<div class="tdst-copy tdst-block">
											${rowItems.map((item) => renderCard(item)).join('')}
										</div>
									</div>
								</div>
							`;
				}).join('');

				rebuildRowStates();
			};

			const showFeedbackSuccess = () => {
				if (typeof window.showSuccessDialog === 'function') {
					window.showSuccessDialog('Feedback Added', 'Your feedback has been added to the testimonials list.');
					return;
				}
				window.alert('Your feedback has been added to the testimonials list.');
			};

			const showFeedbackError = (message) => {
				if (typeof window.showErrorDialog === 'function') {
					window.showErrorDialog('Incomplete Form', message);
					return;
				}
				window.alert(message);
			};

			const updateCurrentRating = (value) => {
				setRating(value);
			};

			if (openButton) {
				openButton.addEventListener('click', (event) => {
					event.preventDefault();
					showModal();
				});
			}

			if (modal) {
				modal.addEventListener('click', (event) => {
					if (event.target && event.target.hasAttribute('data-tdst-close')) {
						hideModal();
					}
				});
			}

			if (resetButton && form) {
				resetButton.addEventListener('click', () => {
					form.reset();
					updateCurrentRating(0);
				});
			}

			if (starButtons.length) {
				starButtons.forEach((button) => {
					button.addEventListener('click', () => {
						updateCurrentRating(Number(button.dataset.tdstStar || '0'));
					});
				});
			}

			if (form) {
				form.addEventListener('submit', (event) => {
					event.preventDefault();

					const formData = new FormData(form);
					const name = String(formData.get('name') || '').trim();
					const role = String(formData.get('role') || '').trim();
					const serviceType = String(formData.get('service_type') || '').trim();
					const comments = String(formData.get('comments') || '').trim();
					const rating = Number(formData.get('overall_rating') || 0);

					if (!name || !role || !serviceType || rating <= 0 || comments.length < 10) {
						showFeedbackError('Please choose a service, add your name and role, select a rating, and write at least 10 characters in your comments.');
						return;
					}

					const wouldRecommend = formData.get('would_recommend') === 'on';
					const testimonial = {
						name,
						role,
						service_type: serviceType,
						quote: comments,
						rating,
						would_recommend: wouldRecommend,
					};

					if (!state.rows.length) {
						state.rows.push([]);
					}

					state.rows[0].unshift(testimonial);
					renderTestimonials();
					form.reset();
					updateCurrentRating(0);
					hideModal();
					showFeedbackSuccess();
				});
			}

			renderTestimonials();

			const tick = (time) => {
				if (state.previousFrameTime == null) state.previousFrameTime = time;

				const dt = Math.max(0, (time - state.previousFrameTime) / 1000);
				state.previousFrameTime = time;

				if (dt > 0) {
					const currentScrollY = window.scrollY;
					const instantVelocity = (currentScrollY - state.previousScrollY) / dt;
					state.previousScrollY = currentScrollY;

					const smoothing = clamp(0, 1, dt * 8);
					state.smoothVelocity += (instantVelocity - state.smoothVelocity) * smoothing;

					const sign = state.smoothVelocity < 0 ? -1 : 1;
					const velocityFactor = sign * Math.min(5, (Math.abs(state.smoothVelocity) / 1000) * 5);
					const speedBoost = Math.min(5, Math.abs(velocityFactor));
					const scrollDirection = velocityFactor >= 0 ? 1 : -1;

					state.rowStates.forEach((rowState) => {
						if (!rowState.inView || rowState.unitWidth <= 0) return;

						const currentDirection = rowState.direction * scrollDirection;
						const pixelsPerSecond = (rowState.unitWidth * rowState.baseVelocity) / 100;
						const moveBy = currentDirection * pixelsPerSecond * (1 + speedBoost) * dt;
						const nextX = rowState.x + moveBy;

						if (nextX >= rowState.unitWidth) {
							rowState.x = nextX % rowState.unitWidth;
						} else if (nextX <= 0) {
							rowState.x = rowState.unitWidth + (nextX % rowState.unitWidth);
						} else {
							rowState.x = nextX;
						}

						rowState.track.style.transform = `translate3d(${-rowState.x}px,0,0)`;
					});
				}

				state.animationFrameId = window.requestAnimationFrame(tick);
			};

			if (!state.animationFrameId) {
				state.animationFrameId = window.requestAnimationFrame(tick);
			}
		};

		document.addEventListener('DOMContentLoaded', () => {
			const roots = document.querySelectorAll('[id^="tdst-"]');
			roots.forEach((root) => initThreeDScrollTrigger(root));
		});
	})();
</script>
@endpush
@endonce