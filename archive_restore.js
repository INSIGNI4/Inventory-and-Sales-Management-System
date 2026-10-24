
(() => {
    'use strict';

    const endpoint = 'archive_restore.php';
    const root = document.getElementById('archive-restore');

    if (!root) return;

    const tbody = document.getElementById('ar-table-body');
    const message = document.getElementById('ar-message');
    const search = document.getElementById('ar-search');
    const refreshButton = document.getElementById('ar-refresh-btn');
    const detailsHeading = document.getElementById('ar-details-heading');
    const identifierHeading = document.getElementById('ar-identifier-heading');

    let currentType = 'product';
    let currentStatus = 'active';
    let records = [];

    const typeLabels = {
        product: {
            details: 'Category',
            identifier: 'Barcode'
        },
        supplier: {
            details: 'Contact Person',
            identifier: 'Email'
        },
        user: {
            details: 'Account Type',
            identifier: 'Email'
        }
    };

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, char => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[char]));
    }

    function setMessage(text, type = '') {
        message.textContent = text;
        message.className = 'ar-message';

        if (type) {
            message.classList.add(type);
        }
    }

    function renderRecords() {
        const keyword = search.value.trim().toLowerCase();

        const filteredRecords = records.filter(record => {
            return [
                record.Record_ID,
                record.Record_Name,
                record.Details,
                record.Identifier
            ].some(value =>
                String(value ?? '').toLowerCase().includes(keyword)
            );
        });

        if (filteredRecords.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="ar-empty">
                        No matching records found.
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = filteredRecords.map(record => {
            const archived = currentStatus === 'archived';

            const statusText = currentType === 'user'
                ? (archived ? 'Deactivated' : 'Active')
                : (archived ? 'Archived' : 'Active');

            const actionText = archived
                ? 'Restore'
                : (currentType === 'user' ? 'Deactivate' : 'Archive');

            const actionIcon = archived
                ? 'fa-rotate-left'
                : (currentType === 'user'
                    ? 'fa-user-slash'
                    : 'fa-box-archive');

            return `
                <tr>
                    <td>${escapeHtml(record.Record_ID)}</td>

                    <td>
                        <strong>${escapeHtml(record.Record_Name || '—')}</strong>
                    </td>

                    <td>${escapeHtml(record.Details || '—')}</td>

                    <td>${escapeHtml(record.Identifier || '—')}</td>

                    <td>
                        <span class="ar-status-pill ${archived ? 'archived' : 'active'}">
                            ${statusText}
                        </span>
                    </td>

                    <td>
                        <button
                            type="button"
                            class="ar-action-btn ${archived ? 'restore' : 'archive'}"
                            data-record-id="${escapeHtml(record.Record_ID)}"
                        >
                            <i class="fa-solid ${actionIcon}"></i>
                            ${actionText}
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    async function loadRecords() {
        refreshButton.disabled = true;
        setMessage('Loading records...');

        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="ar-empty">
                    Loading records...
                </td>
            </tr>
        `;

        try {
            const url =
                `${endpoint}?action=list` +
                `&type=${encodeURIComponent(currentType)}` +
                `&status=${encodeURIComponent(currentStatus)}` +
                `&t=${Date.now()}`;

            const response = await fetch(url, {
                cache: 'no-store',
                credentials: 'same-origin'
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message || 'Unable to load records.'
                );
            }

            records = Array.isArray(result.records)
                ? result.records
                : [];

            detailsHeading.textContent =
                typeLabels[currentType].details;

            identifierHeading.textContent =
                typeLabels[currentType].identifier;

            renderRecords();

            setMessage(
                `Loaded ${records.length} ${currentStatus} ${currentType} record(s).`
            );

        } catch (error) {
            console.error('Archive & Restore loading error:', error);

            records = [];

            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="ar-empty">
                        Failed to load records. Check the PHP endpoint
                        and browser console.
                    </td>
                </tr>
            `;

            setMessage(
                error.message || 'Unable to load records.',
                'error'
            );

        } finally {
            refreshButton.disabled = false;
        }
    }

    // Switch between Products, Suppliers, and Users.
    root.querySelectorAll('[data-ar-type]').forEach(button => {
        button.addEventListener('click', () => {
            currentType = button.dataset.arType;

            root.querySelectorAll('[data-ar-type]').forEach(tab => {
                const selected = tab === button;

                tab.classList.toggle('active', selected);
                tab.setAttribute(
                    'aria-selected',
                    String(selected)
                );
            });

            search.value = '';
            loadRecords();
        });
    });

    // Switch between active and archived/deactivated records.
    root.querySelectorAll('[data-ar-status]').forEach(button => {
        button.addEventListener('click', () => {
            currentStatus = button.dataset.arStatus;

            root.querySelectorAll('[data-ar-status]').forEach(tab => {
                tab.classList.toggle('active', tab === button);
            });

            loadRecords();
        });
    });

    // Search currently loaded records.
    search.addEventListener('input', renderRecords);

    // Refresh the current list.
    refreshButton.addEventListener('click', loadRecords);

    // Archive, deactivate, or restore a record.
    tbody.addEventListener('click', async event => {
        const button = event.target.closest('.ar-action-btn');

        if (!button) return;

        const recordId = Number(button.dataset.recordId);

        if (!Number.isInteger(recordId) || recordId <= 0) {
            setMessage(
                'Invalid record ID. Refresh and try again.',
                'error'
            );
            return;
        }

        const record = records.find(item =>
            Number(item.Record_ID) === recordId
        );

        if (!record) {
            setMessage(
                'Record not found. Refresh and try again.',
                'error'
            );
            return;
        }

        const isRestore = currentStatus === 'archived';

        const action = isRestore ? 'restore' : 'archive';

        const recordName = record.Record_Name || `ID ${recordId}`;

        let confirmationMessage;

        if (currentType === 'user' && !isRestore) {
            confirmationMessage =
                `Deactivate the account "${recordName}"? ` +
                'This user will no longer be able to sign in.';
        } else if (isRestore) {
            confirmationMessage =
                `Restore "${recordName}"? ` +
                'The record will become active again.';
        } else {
            confirmationMessage =
                `Archive "${recordName}"? ` +
                'The record will be retained and not permanently deleted.';
        }

        if (!window.confirm(confirmationMessage)) {
            return;
        }

        button.disabled = true;
        setMessage('Processing request...');

        try {
            const body = new URLSearchParams({
                action: action,
                type: currentType,
                record_id: String(recordId)
            });

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type':
                        'application/x-www-form-urlencoded;charset=UTF-8'
                },
                body: body.toString(),
                credentials: 'same-origin'
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message || 'The requested action failed.'
                );
            }

            const successMessage = result.message ||
                (isRestore
                    ? 'Record restored successfully.'
                    : currentType === 'user'
                        ? 'User account deactivated successfully.'
                        : 'Record archived successfully.');

            await loadRecords();

            setMessage(successMessage, 'success');

        } catch (error) {
            console.error('Archive & Restore action error:', error);

            setMessage(
                error.message || 'The requested action failed.',
                'error'
            );

            button.disabled = false;
        }
    });

    // Load the default view when this script initializes.
    loadRecords();
})();
