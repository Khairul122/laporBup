function uiEnsureToastContainer() {
    var container = document.getElementById('uiToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'uiToastContainer';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '500';
        document.body.appendChild(container);
    }
    return container;
}

function showToast(message, type) {
    type = type || 'success';
    var container = uiEnsureToastContainer();
    var toastId = 'ui-toast-' + Date.now();
    var toast = document.createElement('div');
    var iconHtml = type === 'success'
        ? '<i class="fas fa-check-circle me-2"></i>'
        : '<i class="fas fa-exclamation-circle me-2"></i>';
    var bgColor = type === 'success' ? '#16a34a' : '#dc2626';

    toast.id = toastId;
    toast.className = 'd-flex align-items-center justify-content-between p-3 mb-2 text-white rounded shadow-lg';
    toast.style.cssText = 'background:' + bgColor + ';min-width:300px;animation:uiSlideInRight 0.3s ease-out;';
    toast.innerHTML = '<span>' + iconHtml + message + '</span>' +
        '<button type="button" class="btn-close btn-close-white ms-3" aria-label="Close"></button>';

    toast.querySelector('.btn-close').addEventListener('click', function () {
        uiRemoveToast(toastId);
    });

    container.appendChild(toast);
    setTimeout(function () {
        uiRemoveToast(toastId);
    }, 4000);
}

function uiRemoveToast(toastId) {
    var toast = document.getElementById(toastId);
    if (toast) {
        toast.style.animation = 'uiSlideOutRight 0.3s ease-out';
        setTimeout(function () {
            toast.remove();
        }, 300);
    }
}

function showConfirm(message, onConfirm, options) {
    options = options || {};
    var existing = document.getElementById('uiConfirmModal');
    if (existing) {
        existing.remove();
    }

    var modalEl = document.createElement('div');
    modalEl.className = 'modal fade';
    modalEl.id = 'uiConfirmModal';
    modalEl.tabIndex = -1;
    modalEl.innerHTML =
        '<div class="modal-dialog modal-dialog-centered">' +
        '<div class="modal-content">' +
        '<div class="modal-body p-4 text-center">' +
        '<div class="mb-3"><i class="fas fa-exclamation-triangle text-danger" style="font-size:2.5rem;"></i></div>' +
        '<p class="mb-0">' + message + '</p>' +
        '</div>' +
        '<div class="modal-footer border-0 justify-content-center pb-4">' +
        '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' + (options.cancelText || 'Batal') + '</button>' +
        '<button type="button" class="btn btn-danger" id="uiConfirmOkBtn">' + (options.confirmText || 'Hapus') + '</button>' +
        '</div>' +
        '</div>' +
        '</div>';

    document.body.appendChild(modalEl);
    var modal = new bootstrap.Modal(modalEl);

    modalEl.querySelector('#uiConfirmOkBtn').addEventListener('click', function () {
        modal.hide();
        if (typeof onConfirm === 'function') {
            onConfirm();
        }
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
        modalEl.remove();
    });

    modal.show();
}

function showFieldError(fieldId, message) {
    var field = document.getElementById(fieldId);
    if (!field) {
        return;
    }
    field.classList.add('is-invalid');
    var feedback = field.parentElement.querySelector('.ui-field-error');
    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback ui-field-error';
        field.parentElement.appendChild(feedback);
    }
    feedback.textContent = message;
    feedback.style.display = 'block';
}

function clearFieldError(fieldId) {
    var field = document.getElementById(fieldId);
    if (!field) {
        return;
    }
    field.classList.remove('is-invalid');
    var feedback = field.parentElement.querySelector('.ui-field-error');
    if (feedback) {
        feedback.style.display = 'none';
    }
}
