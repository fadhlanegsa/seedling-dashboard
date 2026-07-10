<?php
/**
 * Reusable Satisfaction Survey Modal + fallback reminder toast
 *
 * Include this partial on any page that renders a "⭐ Beri Penilaian" button
 * (class="btn-rate-request" with data-request-id / data-request-number attributes).
 * Expects an optional $pendingSurveyRequest array (id, request_number) for auto-popup.
 */
$pendingSurveyRequest = $pendingSurveyRequest ?? null;
?>
<div class="modal fade" id="satisfactionSurveyModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="<?= url('public/submit-survey') ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-comment-dots"></i> Bagaimana Pengalaman Anda?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Beri nilai untuk permintaan bibit <strong id="surveyRequestNumber"><?= htmlspecialchars($pendingSurveyRequest['request_number'] ?? '') ?></strong> yang telah Anda ajukan.</p>

                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="request_id" id="surveyRequestId" value="<?= (int)($pendingSurveyRequest['id'] ?? 0) ?>">

                    <div class="form-group text-center">
                        <div class="survey-star-rating">
                            <input type="radio" id="star5" name="rating" value="5" required><label for="star5" title="5 Bintang"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star4" name="rating" value="4"><label for="star4" title="4 Bintang"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star3" name="rating" value="3"><label for="star3" title="3 Bintang"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star2" name="rating" value="2"><label for="star2" title="2 Bintang"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star1" name="rating" value="1"><label for="star1" title="1 Bintang"><i class="fas fa-star"></i></label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="surveyComment">Ulasan (opsional)</label>
                        <textarea class="form-control" id="surveyComment" name="comment" rows="3" placeholder="Ceritakan pengalaman Anda..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Nanti Saja</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Kirim Ulasan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Fallback reminder banner (Google Maps Review style) shown after the modal is dismissed without submitting -->
<div id="surveyReminderToast" class="survey-reminder-toast" style="display:none;">
    <i class="fas fa-star text-warning"></i>
    <span>Yuk, beri penilaian untuk permintaan <strong id="reminderRequestNumber"></strong>!</span>
    <button type="button" class="btn btn-sm btn-primary ml-2" id="reminderRateBtn">Beri Penilaian</button>
    <button type="button" class="close ml-2" id="reminderCloseBtn" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>

<style>
.survey-star-rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: center;
    gap: 4px;
    font-size: 2rem;
}
.survey-star-rating input {
    display: none;
}
.survey-star-rating label {
    color: #dcdcdc;
    cursor: pointer;
    transition: color 0.15s ease;
}
.survey-star-rating input:checked ~ label,
.survey-star-rating label:hover,
.survey-star-rating label:hover ~ label {
    color: #ffc107;
}
.survey-reminder-toast {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1080;
    background: #fff;
    border-radius: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.18);
    padding: 0.6rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    max-width: 90vw;
    font-size: 0.9rem;
}
@media (max-width: 576px) {
    .survey-reminder-toast {
        bottom: 75px;
        flex-wrap: wrap;
        justify-content: center;
        text-align: center;
    }
    .btn-rate-request {
        white-space: normal;
    }
}
</style>

<script nonce="<?= cspNonce() ?>">
document.addEventListener('DOMContentLoaded', function () {
    var modal = $('#satisfactionSurveyModal');
    var requestIdInput = document.getElementById('surveyRequestId');
    var requestNumberEl = document.getElementById('surveyRequestNumber');
    var reminderToast = document.getElementById('surveyReminderToast');
    var reminderRequestNumber = document.getElementById('reminderRequestNumber');
    var pendingRequestId = <?= (int)($pendingSurveyRequest['id'] ?? 0) ?>;
    var pendingRequestNumber = <?= json_encode($pendingSurveyRequest['request_number'] ?? '') ?>;

    function openSurveyModal(requestId, requestNumber) {
        requestIdInput.value = requestId;
        requestNumberEl.textContent = requestNumber;
        reminderToast.style.display = 'none';
        modal.modal('show');
    }

    // Manual trigger via "Beri Penilaian" button on any eligible row
    document.querySelectorAll('.btn-rate-request').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openSurveyModal(this.dataset.requestId, this.dataset.requestNumber);
        });
    });

    // Auto-popup for the latest submitted request that hasn't been surveyed yet
    if (pendingRequestId > 0) {
        openSurveyModal(pendingRequestId, pendingRequestNumber);
    }

    // Fallback UI: if the user dismisses the modal without submitting, show a small
    // reminder toast (Google Maps review style) instead of losing the prompt entirely
    modal.on('hidden.bs.modal', function () {
        if (modal.data('submitted')) return;
        var currentRequestId = requestIdInput.value;
        var currentRequestNumber = requestNumberEl.textContent;
        if (currentRequestId && currentRequestId !== '0') {
            reminderRequestNumber.textContent = currentRequestNumber;
            reminderToast.dataset.requestId = currentRequestId;
            reminderToast.dataset.requestNumber = currentRequestNumber;
            reminderToast.style.display = 'flex';
        }
    });

    modal.find('form').on('submit', function () {
        modal.data('submitted', true);
    });

    document.getElementById('reminderRateBtn').addEventListener('click', function () {
        openSurveyModal(reminderToast.dataset.requestId, reminderToast.dataset.requestNumber);
    });

    document.getElementById('reminderCloseBtn').addEventListener('click', function () {
        reminderToast.style.display = 'none';
    });
});
</script>
