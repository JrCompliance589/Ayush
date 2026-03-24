<template>
    <!-- Modal Overlay - Controlled by Parent -->
    <transition name="modal-fade">
        <div v-if="isOpen && itemData" class="modal-overlay" @click.self="closeModal">
            <div class="modal-container">
                <!-- Modal Header -->
                <div class="modal-header">
                    <div class="header-content">
                        <div class="header-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="6" width="20" height="12" rx="2" />
                                <path d="M2 10h20" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="modal-title">Payment Receipts</h2>
                            <p class="modal-subtitle">{{ itemData?.company_name }} • {{ itemData?.proforma_number }}</p>
                        </div>
                    </div>
                    <button @click="closeModal" class="close-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div v-if="!itemData?.payment_ledger?.length" class="empty-state">
                        <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 6v6l4 2" />
                        </svg>
                        <p>No payment records found</p>
                    </div>

                    <div v-else class="payments-grid">
                        <div v-for="(payment, index) in itemData.payment_ledger" :key="payment.id" class="payment-card">
                            <!-- Payment Header -->
                            <div class="payment-header">
                                <div class="payment-badge">
                                    <span class="badge-number">#{{ index + 1 }}</span>
                                </div>
                                <div class="payment-status">
                                    <svg class="status-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Paid</span>
                                </div>
                            </div>

                            <!-- Payment Details -->
                            <div class="payment-details">
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <svg class="detail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Amount
                                    </div>
                                    <div class="detail-value amount">₹{{ formatCurrency(payment.paid_amount) }}</div>
                                </div>

                                <div class="detail-row">
                                    <div class="detail-label">
                                        <svg class="detail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <rect x="2" y="6" width="20" height="12" rx="2" />
                                            <path d="M2 10h20" />
                                        </svg>
                                        Method
                                    </div>
                                    <div class="detail-value">{{ payment.payment_mode }}</div>
                                </div>

                                <div class="detail-row">
                                    <div class="detail-label">
                                        <svg class="detail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                            <path d="M16 2v4M8 2v4M3 10h18" />
                                        </svg>
                                        Date
                                    </div>
                                    <div class="detail-value">{{ formatDate(payment.paid_at) }}</div>
                                </div>

                                <div class="detail-row">
                                    <div class="detail-label">
                                        <svg class="detail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path
                                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                        </svg>
                                        Reference
                                    </div>
                                    <div class="detail-value reference">{{ payment.reference }}</div>
                                </div>

                                <!-- Financial Breakdown -->
                                <div class="breakdown">
                                    <div class="breakdown-item">
                                        <span class="breakdown-label">Base Amount:</span>
                                        <span class="breakdown-value">₹{{ formatCurrency(payment.amount) }}</span>
                                    </div>
                                    <div v-if="payment.tds_rate > 0 && payment.tds_amount > 0" class="breakdown-item">
                                        <span class="breakdown-label">TDS:</span>
                                        <span class="breakdown-value text-danger">-₹{{
                                            formatCurrency(payment.tds_amount) }}</span>
                                    </div>
                                    <div v-if="payment.has_payment_exception > 0 && payment.exception_amount > 0" class="breakdown-item">
                                        <span class="breakdown-label">Exception Amount:</span>
                                        <span :class="payment.exception_type === 'excess' ? 'breakdown-value text-save' : 'breakdown-value text-danger'">{{ payment.exception_type === 'excess' ? "+" : "-" }}₹{{
                                            formatCurrency(payment.excess_or_short_amount) }}</span>
                                    </div>
                                    <div class="breakdown-item">
                                        <span class="breakdown-label">Total Amount:</span>
                                        <span class="breakdown-value">₹{{ formatCurrency(payment.paid_amount) }}</span>
                                    </div>
                                    <!-- Toggle Button -->
                                    <div v-if="payment.has_payment_exception || payment.tds_manually_edited"
                                        class="exception-toggle" @click="() => handleToggle(payment.id)">
                                        <span>⚠️ Payment Adjustment</span>
                                        <span class="toggle-text">
                                            {{ showException === payment.id ? 'Hide details' : 'View details' }}
                                        </span>
                                    </div>

                                    <!-- Collapsible Card -->
                                    <transition name="slide-fade">
                                        <div v-if="showException === payment.id" class="exception-card">
                                            <!-- Payment Exception -->
                                            <div v-if="payment.has_payment_exception" class="exception-section">
                                                <div class="exception-row">
                                                    <span class="label">Exception Type</span>
                                                    <span class="value badge badge-warning">
                                                        {{ payment.exception_type.toUpperCase() }}
                                                    </span>
                                                </div>

                                                <div class="exception-row">
                                                    <span class="label">Exception Amount</span>
                                                    <span :class="payment.exception_type === 'excess' ? 'value text-save' : 'value text-danger'">
                                                        {{ payment.exception_type === 'excess' ? "+" : "-" }}₹{{ formatCurrency(payment.excess_or_short_amount) }}
                                                    </span>
                                                </div>

                                                <div class="exception-reason">
                                                    <span class="label">Reason</span>
                                                    <p class="reason-text">{{ payment.exception_reason }}</p>
                                                </div>
                                            </div>

                                            <!-- Manual TDS -->
                                            <div v-if="payment.tds_manually_edited" class="manual-tds">
                                                ✏️ <strong>TDS was manually edited</strong>
                                                <span class="subtext">(Auto-calculation overridden)</span>
                                            </div>
                                        </div>
                                    </transition>

                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="payment-action">
                                <button
                                    v-if="payment.payment_invoice_pdf_url === undefined || payment.payment_invoice_pdf_url === null"
                                    @click="handleGenerateReceipt(payment, itemData)" class="action-btn generate">
                                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Generate Receipt
                                </button>
                                <button v-else @click="handleShareReceipt(payment, itemData)" class="action-btn share">
                                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path
                                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                    </svg>
                                    Share Receipt
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    name: 'PaymentReceiptModal',
    props: {
        itemData: {
            type: Object,
            default: null
        },
        isOpen: {
            type: Boolean,
            default: false
        }
    },
    emits: ['close', 'generate-receipt', 'share-receipt'],
    watch: {
        isOpen(newVal) {
            if (newVal) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
    },
    data() {
        return {
            showException: null
        }
    },

    methods: {
        closeModal() {
            this.$emit('close');
        },
        formatCurrency(value) {
            return new Intl.NumberFormat('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value);
        },
        formatDate(dateString) {
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('en-IN', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).format(date);
        },
        handleToggle(id) {
            this.showException = this.showException === id ? null : id;
        },

        handleGenerateReceipt(paymentData, invoiceData) {

            const paymentTermsSlabs = invoiceData?.payment_terms?.slabs || [];
            const paymentLedgerSlabs = paymentData?.slab_ids || [];


            const lastRemarks =
                paymentTermsSlabs
                    ?.filter(
                        slab =>
                            paymentLedgerSlabs.includes(slab.id) &&
                            slab.what_to_show === "remarks" &&
                            slab?.remarks?.trim()
                    )
                    ?.at(-1)?.remarks || "";



            const receiptItem = {
                id: invoiceData.id,
                contact_person: invoiceData.contact_person,
                company_name: invoiceData.company_name,
                email: invoiceData.email,
                phone: invoiceData.phone,
                gst_number: invoiceData.gst_number,
                address: invoiceData.address,
                proforma_number: invoiceData.proforma_number,
                ledger_id: paymentData.id,
                payment_id: paymentData.reference,
                paid_at: paymentData.paid_at,
                payment_method: paymentData.method,
                amount: paymentData.amount,
                payment_terms_remarks: lastRemarks,
                slab_ids: paymentData.slab_ids || [],
                tds_rate: paymentData.tds_rate,
                tds_amount: paymentData.tds_amount,
                total_amount: paymentData.paid_amount,
                additional_fee: invoiceData.additional_fee,
                sub_total: invoiceData.sub_total,
                discount_amount: invoiceData.discount_amount,
                GST_amount: invoiceData.GST_amount,
                total: invoiceData.total,
                excess_or_short_amount: paymentData.excess_or_short_amount,
                amount_after_discount: invoiceData.amount_after_discount,
                has_payment_exception: paymentData.has_payment_exception,
                exception_amount: paymentData.exception_amount,
                exception_type: paymentData.exception_type,
                exception_reason: paymentData.exception_reason,
                platform_charge: invoiceData.platform_charge,
                discount: invoiceData.discount,
                wallet_recharge: invoiceData.wallet_recharge,
                setup_fee: invoiceData.setup_fee,
                customization_fee: invoiceData.customization_fee,
                service_charge_hsn_sac: invoiceData.service_charge_hsn_sac,
                platform_charge_type: invoiceData.platform_charge_type,
                ...invoiceData
            }
            this.$emit('generate-receipt', receiptItem);
        },
        handleShareReceipt(paymentData, invoiceData) {
            const receiptItem = {
                contact_person: invoiceData.contact_person,
                company_name: invoiceData.company_name,
                phone: invoiceData.phone,
                email: invoiceData.email,
                invoiceNumber: paymentData.reference,
                templateType: "Payment Receipt",
                templateName: "payment_receipt",
                pdf: paymentData.payment_invoice_pdf_url,
                pdfDownloadUrl: `${paymentData.payment_invoice_pdf_url}/download`,
                pdfName: `PaymentReceipt_${paymentData.reference.replace('/', '_')}_${invoiceData.company_name.replace(/[^a-zA-Z0-9]/g, '_')}.pdf`
            }
            this.$emit('share-receipt', receiptItem);
        }
    },
    beforeUnmount() {
        // Clean up on component unmount
        document.body.style.overflow = '';
    }
};
</script>

<style scoped>
* {
    box-sizing: border-box;
}

/* Modal Overlay */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 20px;
}

.modal-container {
    background: white;
    border-radius: 16px;
    max-width: 1000px;
    width: 100%;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Modal Header */
.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 24px 28px;
    border-bottom: 1px solid #e5e7eb;
    background: linear-gradient(135deg, #fff5f0 0%, #ffffff 100%);
}

.header-content {
    display: flex;
    align-items: center;
    gap: 16px;
}

.header-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #ff5100 0%, #ff7733 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.header-icon svg {
    width: 24px;
    height: 24px;
    color: white;
}

.modal-title {
    font-size: 22px;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.modal-subtitle {
    font-size: 14px;
    color: #6b7280;
    margin: 4px 0 0 0;
}

.close-btn {
    width: 36px;
    height: 36px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.close-btn:hover {
    background: #f9fafb;
    border-color: #ff5100;
}

.close-btn svg {
    width: 20px;
    height: 20px;
    color: #6b7280;
}

.close-btn:hover svg {
    color: #ff5100;
}

/* Modal Body */
.modal-body {
    padding: 24px 28px;
    overflow-y: auto;
    flex: 1;
}

/* Empty State */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    color: #9ca3af;
}

.empty-icon {
    width: 64px;
    height: 64px;
    margin-bottom: 16px;
    opacity: 0.5;
}

/* Payments Grid */
.payments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
}

/* Payment Card */
.payment-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: fit-content;
}

.payment-card:hover {
    box-shadow: 0 8px 24px rgba(255, 81, 0, 0.15);
    border-color: #ff5100;
}

/* Payment Header */
.payment-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    background: linear-gradient(135deg, #fff5f0 0%, #ffffff 100%);
    border-bottom: 1px solid #fee2d5;
}

.payment-badge {
    background: linear-gradient(135deg, #ff5100 0%, #ff7733 100%);
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    color: white;
}

.badge-number {
    letter-spacing: 0.5px;
}

.payment-status {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #059669;
    font-size: 13px;
    font-weight: 600;
}

.status-icon {
    width: 18px;
    height: 18px;
}

/* Payment Details */
.payment-details {
    padding: 20px;
}

.detail-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
}

.detail-row:last-of-type {
    border-bottom: none;
}

.detail-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #6b7280;
    font-weight: 500;
}

.detail-icon {
    width: 16px;
    height: 16px;
    color: #9ca3af;
}

.detail-value {
    font-size: 14px;
    color: #111827;
    font-weight: 600;
}

.detail-value.amount {
    font-size: 16px;
    color: #ff5100;
}

.detail-value.reference {
    font-family: 'Courier New', monospace;
    font-size: 12px;
    background: #f3f4f6;
    padding: 4px 8px;
    border-radius: 4px;
}

/* Breakdown */
.breakdown {
    margin-top: 16px;
    padding: 16px;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.breakdown-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    font-size: 13px;
}

.breakdown-label {
    color: #6b7280;
}

.breakdown-value {
    font-weight: 600;
    color: #111827;
}

.text-danger {
    color: #dc2626 !important;
}

.text-save {
    color: rgba(7, 137, 7, 0.766) !important;
}

/* Payment Action */
.payment-action {
    padding: 16px 20px;
    background: #fafafa;
    border-top: 1px solid #e5e7eb;
}

.action-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.action-btn.generate {
    background: linear-gradient(135deg, #ff5100 0%, #ff7733 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(255, 81, 0, 0.3);
}

.action-btn.generate:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 81, 0, 0.4);
}

.action-btn.share {
    background: white;
    color: #ff5100;
    border: 2px solid #ff5100;
}

.action-btn.share:hover {
    background: #fff5f0;
    transform: translateY(-2px);
}

.btn-icon {
    width: 18px;
    height: 18px;
}

/* Transitions */
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .payments-grid {
        grid-template-columns: 1fr;
    }

    .modal-container {
        max-height: 95vh;
        margin: 10px;
    }

    .modal-header {
        padding: 20px;
    }

    .modal-body {
        padding: 20px;
    }
}

.exception-card {
    margin-top: 16px;
    border: 1px solid #f5c2c7;
    background: #fff5f5;
    border-radius: 10px;
    padding: 16px;
    font-size: 14px;
}

.exception-header {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #842029;
    margin-bottom: 12px;
}

.exception-icon {
    font-size: 18px;
}

.exception-section {
    display: grid;
    row-gap: 8px;
}

.exception-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.label {
    color: #6c757d;
    font-weight: 500;
}

.value {
    font-weight: 600;
}

.text-danger {
    color: #dc3545;
}

.badge {
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 12px;
}

.badge-warning {
    background: #fff3cd;
    color: #664d03;
}

.exception-reason {
    margin-top: 8px;
}

.reason-text {
    margin-top: 4px;
    background: #ffffff;
    border: 1px dashed #f1aeb5;
    padding: 8px;
    border-radius: 6px;
    color: #842029;
}

.manual-tds {
    margin-top: 12px;
    padding: 10px;
    border-left: 4px solid #ff5100;
    background: #e7f1ff;
    color: #ff5100;
    font-size: 13px;
}

.manual-tds .subtext {
    font-size: 12px;
    color: #495057;
}

.exception-toggle {
    margin-top: 12px;
    padding: 10px 12px;
    border-radius: 8px;
    background: #fff3cd;
    border: 1px solid #ffe69c;
    color: #664d03;
    display: flex;
    justify-content: space-between;
    cursor: pointer;
    font-weight: 600;
}

.toggle-text {
    font-size: 13px;
    text-decoration: underline;
}

.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 0.25s ease;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}
</style>