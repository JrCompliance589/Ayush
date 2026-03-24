<template>
    <div v-if="props.isOpen" class="modal-overlay" @click.self="closeModal">
        <div class="modal-container">
            <!-- Modal Header -->
            <div class="modal-header">
                <h1 class="modal-title">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" class="text-primary"
                        stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Update Quotation
                </h1>
                <button @click="closeModal" class="close-btn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <p class="subtitle">Update the details below to regenerate your quotation</p>

                <div class="form-grid">
                    <div class="form-group">
                        <FormInput v-model="formData.contactPerson" :name="'contactPerson'" :label="'Contact Person'"
                            :error="errors.contactPerson" type="text" :required="true" />
                    </div>
                    <div class="form-group">
                        <FormInput v-model="formData.email" :name="'Email'" :label="'Email'" :error="errors.email"
                            type="email" :required="true" />
                    </div>
                    <div class="form-group pt-1">
                        <FormPhoneInput v-model="formData.phone" :name="'Phone'" :label="'Phone'" :error="errors.phone"
                            :required="true" />
                    </div>

                    <div class="form-group">
                        <FormDateInput v-model="formData.quotation_valid_until_date" name="quotation_valid_until_date"
                            label="Quotation Valid Until" :required="true" placeholder="Select validity date"
                            :error-message="errors.quotation_valid_until_date" />
                    </div>

                    <div class="form-group">
                        <FormInput v-model="formData.signature" :name="'signature'" :label="'Signature'"
                            :error="errors.signature" type="text" :required="true" />
                    </div>

                    <div class="form-group !py-0">
                        <label class="!py-0">Designation <span class="required">*</span></label>
                        <select v-model="formData.designation" class="!py-2.5">
                            <option value="" disabled>Select designation</option>
                            <option v-for="d in Designations" :key="d" :value="d">{{ d }}</option>
                        </select>
                        <span v-if="errors.designation" class="error-text">{{ errors.designation }}</span>
                    </div>
                </div>

                <div class="rounded-md border bg-slate-50/80 border-slate-300 p-6">
                    <div class="form-grid">
                        <div class="form-group relative">
                            <label>GST Number</label>
                            <input type="text" v-model="formData.gst_number" placeholder="Enter GST Number"
                                maxlength="15" :class="{ 'error-input': errors.gst_number }">
                            <button @click="verifyGst"
                                :disabled="gstState.state === 'Loading' || formData.gst_number.length !== 15"
                                class="absolute right-[5px] top-[35px] font-semibold bg-primary/90 disabled:bg-primary/60 disabled:cursor-not-allowed disabled:scale-100 text-white text-sm py-2 px-4 rounded-md transition-all duration-300 hover:scale-[1.01] shadow-sm flex items-center gap-1">
                                {{ gstState.state === 'Loading' ? 'Verifying...' : 'Verify GST' }}
                            </button>
                            <span v-if="gstState.state === 'Success'" class="text-green-600 text-xs pt-2">{{
                                gstState.message }}</span>
                            <span v-if="gstState.state === 'Loading'" class="text-purple-600 text-xs pt-2">{{
                                gstState.message }}</span>
                            <span v-if="gstState.state === 'Error' || errors.gst_number"
                                class="text-red-600 text-xs pt-2">{{ gstState.message?.trim() || errors.gst_number
                                }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-9 justify-center items-center gap-1 text-slate-300 pt-4 pb-2">
                        <div class="col-span-4 h-[1px] bg-slate-300"></div>
                        <span class="col-span-1 text-center">OR</span>
                        <div class="col-span-4 h-[1px] bg-slate-300"></div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Company Name <span class="required">*</span></label>
                            <input type="text" v-model="formData.companyName" placeholder="Enter company name"
                                :class="{ 'error-input': errors.companyName }">
                            <span v-if="errors.companyName" class="error-text">{{ errors.companyName }}</span>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea v-model="formData.address" placeholder="Enter complete address"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Terms & Conditions -->
                <div class="section-title">Terms & Conditions</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="!py-0">Terms & Conditions <span class="required">*</span></label>
                        <select v-model="formData.termsAndConditions" class="!py-2.5" :disabled="isLoadingTerms">
                            <option value="" disabled>{{ isLoadingTerms ? 'Loading...' : 'Select Terms & Conditions' }}
                            </option>
                            <option v-for="terms in termsAndConditionsList" :key="terms.id" :value="terms.id">
                                {{ terms.name }}{{ terms.is_primary ? ' ⭐' : '' }}
                            </option>
                        </select>
                        <span v-if="errors.termsAndConditions" class="error-text">{{ errors.termsAndConditions }}</span>
                    </div>
                </div>

                <!-- Service Charges -->
                <div class="section-title">Service Charges</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Platform Charge Type</label>
                        <select v-model="formData.selectedPlanId" @change="updatePlatformChargeDetails">
                            <option value="">Select Plan</option>
                            <option v-for="plan in activePlans" :key="plan.id" :value="plan.id">
                                {{ plan.name }} - ₹{{ formatCurrency(plan.price) }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Platform Charge Amount (₹)</label>
                        <input type="number" v-model="formData.platformCharge" placeholder="0" min="0" step="0.01"
                            readonly style="background-color: #f9fafb; cursor: not-allowed;">
                    </div>
                    <div class="form-group">
                        <label>Wallet Recharge (₹)</label>
                        <input type="number" v-model="formData.walletRecharge" placeholder="0" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Setup Fee (₹)</label>
                        <input type="number" v-model="formData.setupFee" placeholder="0" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Customization Fee (₹)</label>
                        <input type="number" v-model="formData.customizationFee" placeholder="0" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>HSN/SAC Code</label>
                        <input type="text" v-model="formData.serviceChargeHsnSac" placeholder="HSN/SAC Code">
                    </div>
                    <div class="form-group">
                        <label>GST Rate (%)</label>
                        <input type="number" v-model="formData.serviceChargeGstRate" placeholder="18" min="0" max="100"
                            step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Discount (%)</label>
                        <input type="number" v-model="formData.discount" placeholder="0" min="0" max="100" step="0.01">
                    </div>
                </div>

                <!-- Additional Items -->
                <div class="additional-items">
                    <div class="additional-header">
                        <div class="section-title no-border">Additional Items / Products (Optional)</div>
                        <button class="btn btn-secondary" :disabled="additionalItems.length >= 5"
                            @click="addAdditionalItem">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add Item
                        </button>
                    </div>

                    <div class="product-filters" v-if="additionalItems.length > 0">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Search Products</label>
                                <input type="text" v-model="productSearchQuery" @input="fetchProducts"
                                    placeholder="Search by name, category or hsn/sac...">
                            </div>
                            <div class="form-group">
                                <label>Filter by Category</label>
                                <select v-model="selectedCategory" @change="fetchProducts">
                                    <option value="">All Categories</option>
                                    <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div v-for="(item, index) in additionalItems" :key="index" class="item-row-extended">
                        <div class="form-group full-width">
                            <label>Select Product (Optional)</label>
                            <select v-model="item.productId" @change="handleProductSelect(index, item.productId)"
                                :disabled="isLoadingProducts">
                                <option value="">{{ isLoadingProducts ? 'Loading products...' : `Select a product or
                                    enter custom` }}</option>
                                <option v-for="product in products" :key="product.id" :value="product.id">
                                    {{ product.name }} - {{ product.category }} - ₹{{ formatCurrency(product.amount) }}
                                    (HSN: {{ product.hsn_sac }}, GST: {{ product.gst_rate }}%)
                                </option>
                            </select>
                        </div>
                        <div class="item-details-grid">
                            <div class="form-group">
                                <label>Description <span class="required">*</span></label>
                                <input type="text" v-model="item.description" placeholder="Item description">
                            </div>
                            <div class="form-group">
                                <label>HSN/SAC Code</label>
                                <input type="text" v-model="item.hsn_sac" placeholder="HSN/SAC">
                            </div>
                            <div class="form-group">
                                <label>Quantity</label>
                                <input type="number" v-model="item.quantity" @input="handleQuantityChange(index)"
                                    placeholder="1" min="1" step="1">
                            </div>
                            <div class="form-group">
                                <label>Unit Price (₹)</label>
                                <input type="number" v-model="item.unit_price" @input="handleQuantityChange(index)"
                                    placeholder="0" min="0" step="0.01">
                            </div>
                            <div class="form-group">
                                <label>Discount (%)</label>
                                <input type="number" v-model="item.discount" @input="handleQuantityChange(index)"
                                    placeholder="0" min="0" max="100" step="0.01">
                            </div>
                            <div class="form-group">
                                <label>GST Rate (%)</label>
                                <input type="number" v-model="item.gst_rate" @input="handleQuantityChange(index)"
                                    placeholder="18" min="0" max="100" step="0.01">
                            </div>
                            <div class="form-group">
                                <label>Total Amount (₹)</label>
                                <input type="number" v-model="item.amount" placeholder="0" readonly
                                    style="background-color: #f9fafb; cursor: not-allowed;">
                            </div>
                        </div>
                        <button class="btn btn-danger" @click="removeAdditionalItem(index)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path
                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                </path>
                            </svg>
                            Remove Item
                        </button>
                    </div>
                </div>

                <!-- Summary -->
                <div class="summary-box">
                    <h3 class="summary-title">Quotation Summary</h3>
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>₹{{ formatCurrency(calculateSubtotal()) }}</span>
                    </div>
                    <div class="summary-row" v-if="calculateDiscount() > 0">
                        <span>Discount:</span>
                        <span class="text-red-600">-₹{{ formatCurrency(calculateDiscount()) }}</span>
                    </div>
                    <div class="summary-row" v-if="calculateDiscount() > 0">
                        <span>Amount After Discount:</span>
                        <span>₹{{ formatCurrency(calculateAmountAfterDiscount()) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>GST:</span>
                        <span>₹{{ formatCurrency(calculateGST()) }}</span>
                    </div>
                    <div class="summary-row total">
                        <span>TOTAL:</span>
                        <span>₹{{ formatCurrency(calculateTotal()) }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="btn-group">
                    <button class="btn btn-outline" @click="closeModal">Cancel</button>
                    <button class="btn btn-primary" @click="handleSubmit" :disabled="isSubmitting">
                        <svg v-if="!isSubmitting" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        <span v-if="isSubmitting">Updating...</span>
                        <span v-else>Update Quotation</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Share Options Modal -->
    <div v-if="showShareOptions" class="modal-overlay" @click.self="closeShareModal">
        <div class="share-modal">
            <div class="share-header">
                <h2 class="share-title">Share Quotation</h2>
                <button @click="closeShareModal" class="close-btn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="share-body">
                <p class="share-subtitle">Choose how you want to share this quotation</p>
                <div class="share-options">
                    <button @click="shareOnFreeWhatsApp" :disabled="isSharing.freeWhatsapp"
                        class="share-option whatsapp">
                        <div class="share-icon-wrapper whatsapp-bg">
                            <svg v-if="!isSharing.freeWhatsapp" width="28" height="28" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                                </path>
                            </svg>
                            <svg v-else class="animate-spin" width="28" height="28" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="2" x2="12" y2="6"></line>
                                <line x1="12" y1="18" x2="12" y2="22"></line>
                                <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
                                <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
                                <line x1="2" y1="12" x2="6" y2="12"></line>
                                <line x1="18" y1="12" x2="22" y2="12"></line>
                                <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
                                <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
                            </svg>
                        </div>
                        <div class="share-content">
                            <h3>Share on WhatsApp (Direct)</h3>
                            <p>{{ isSharing.freeWhatsapp ? 'Sharing...' : 'Send pdf when chat is open' }}</p>
                        </div>
                    </button>

                    <button @click="shareOnWhatsApp" :disabled="isSharing.whatsapp" class="share-option whatsapp">
                        <div class="share-icon-wrapper whatsapp-bg">
                            <svg v-if="!isSharing.whatsapp" width="28" height="28" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                                </path>
                            </svg>
                            <svg v-else class="animate-spin" width="28" height="28" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="2" x2="12" y2="6"></line>
                                <line x1="12" y1="18" x2="12" y2="22"></line>
                                <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
                                <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
                                <line x1="2" y1="12" x2="6" y2="12"></line>
                                <line x1="18" y1="12" x2="22" y2="12"></line>
                                <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
                                <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
                            </svg>
                        </div>
                        <div class="share-content">
                            <h3>Share on WhatsApp (Template)</h3>
                            <p>{{ isSharing.whatsapp ? 'Sharing...' : 'Send pdf when chat is not open yet' }}</p>
                        </div>
                    </button>

                    <button @click="shareViaEmail" :disabled="isSharing.email || !currentPdfData?.email"
                        class="share-option email">
                        <div class="share-icon-wrapper email-bg">
                            <svg v-if="!isSharing.email" width="28" height="28" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                </path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            <svg v-else class="animate-spin" width="28" height="28" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="2" x2="12" y2="6"></line>
                                <line x1="12" y1="18" x2="12" y2="22"></line>
                                <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
                                <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
                                <line x1="2" y1="12" x2="6" y2="12"></line>
                                <line x1="18" y1="12" x2="22" y2="12"></line>
                                <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
                                <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
                            </svg>
                        </div>
                        <div class="share-content">
                            <h3>Share via Email</h3>
                            <p v-if="!currentPdfData?.email" class="text-red-500">Email not provided</p>
                            <p v-else-if="isSharing.email">Sending...</p>
                            <p v-else>Send to {{ currentPdfData?.email }}</p>
                        </div>
                    </button>

                    <button @click="downloadPDF" :disabled="isSharing.downloading" class="share-option download">
                        <div class="share-icon-wrapper download-bg">
                            <svg v-if="!isSharing.downloading" width="28" height="28" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            <svg v-else class="animate-spin" width="28" height="28" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="2" x2="12" y2="6"></line>
                                <line x1="12" y1="18" x2="12" y2="22"></line>
                                <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
                                <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
                                <line x1="2" y1="12" x2="6" y2="12"></line>
                                <line x1="18" y1="12" x2="22" y2="12"></line>
                                <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
                                <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
                            </svg>
                        </div>
                        <div class="share-content">
                            <h3 v-if="isSharing.downloading">Downloading...</h3>
                            <h3 v-else>Download</h3>
                            <p>Save copy of the PDF</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quotation PDF Template (Hidden) -->
    <div v-if="currentPdfData" ref="pdfTemplate" id="uq-pdf-template" class="pdf-content relative pb-8">
        <div class="pdf-page">
            <div class="pdf-header">
                <div class="company-info">
                    <img src="../../images/nyifeBrand.png" alt="nyife-logo" width='220' height="73"
                        style="width: 220px !important; height: 73px !important;" />
                    <h2>Complia Services Ltd</h2>
                    <p>nyife.chat | info@nyife.chat | +91 11 430 22 315 </p>
                    <p>Plot no.9, Third Floor, Paschim Vihar Extn. Delhi-110063, India</p>
                    <p>GSTIN: 07AALCC1963C1ZT | CIN No: U70200DL2023PLC417528</p>
                </div>
                <div class="invoice-info">
                    <h3>QUOTATION</h3>
                    <p><strong>Quotation #:</strong> {{ currentPdfData?.quotation_number }}</p>
                    <p><strong>Date:</strong> {{ currentPdfData?.quotation_date }}</p>
                    <p><strong>Valid Until:</strong> {{ currentPdfData?.quotation_valid_until_date }}</p>
                </div>
            </div>

            <div class="client-info">
                <h4>Bill To:</h4>
                <p><strong>Mr/Ms: {{ currentPdfData?.contact_person || 'N/A' }}</strong></p>
                <p><strong>Company:</strong> {{ currentPdfData?.company_name || 'N/A' }}</p>
                <p><strong>Phone:</strong> {{ currentPdfData?.phone || 'N/A' }}</p>
                <p v-if="currentPdfData?.email?.trim()"><strong>Email:</strong> {{ currentPdfData?.email || 'N/A' }}</p>
                <p class="w-[70%]" v-if="currentPdfData?.address?.trim()"><strong>Address:</strong> {{
                    currentPdfData?.address || 'N/A' }}</p>
                <p v-if="currentPdfData?.gst_number?.trim()"><strong>GSTIN:</strong> {{ currentPdfData?.gst_number ||
                    'N/A' }}</p>
            </div>

            <div class="thank-you-note">
                <p class="!text-base">
                    <strong>Dear {{ currentPdfData?.contact_person || 'Sir/Madam' }},</strong>
                    Thank you for considering nyife.chat for your communication needs. We appreciate the
                    opportunity to share our quotation and hope you find our platform innovative, reliable,
                    and competitively priced. We look forward to building a long-term partnership and
                    contributing to your business growth.
                </p>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Description</th>
                        <th style="width: 90px;">HSN/SAC</th>
                        <th style="width: 60px; text-align: center;">Qty</th>
                        <th style="width: 90px; text-align: right;">Rate</th>
                        <th style="width: 70px; text-align: center;">Discount</th>
                        <th style="width: 70px; text-align: center;">GST</th>
                        <th style="width: 110px; text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, index) in getVisibleItems(currentPdfData, currentPdfData?.additional_fee)"
                        :key="index">
                        <td>{{ index + 1 }}</td>
                        <td>{{ row?.description }}</td>
                        <td style="text-align: center;">{{ row?.hsn_sac || 'N/A' }}</td>
                        <td style="text-align: center;">{{ row?.quantity || 1 }}</td>
                        <td style="text-align: right;">{{ formatCurrency(row?.unit_price) }}</td>
                        <td style="text-align: right;">({{ row?.discount ?? 0 }}%) {{
                            formatCurrency(row?.discount_amount) }}</td>
                        <td style="text-align: right;">({{ row?.gst_rate ?? 18 }}%) {{ formatCurrency(row?.gst_amount)
                        }}</td>
                        <td style="text-align: right;">{{ formatCurrency(row?.amount) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="summary-section">
                <table class="summary-table-wrapper">
                    <thead class="summary-table">
                        <tr>
                            <td>SUBTOTAL:</td>
                            <td>₹{{ formatCurrency(currentPdfData?.sub_total) }}</td>
                        </tr>
                        <tr v-if="currentPdfData?.discount > 0">
                            <td>DISCOUNT:</td>
                            <td class="discount-amount">-₹{{ formatCurrency(currentPdfData?.discount_amount) }}</td>
                        </tr>
                        <tr v-if="currentPdfData?.discount > 0">
                            <td>AMOUNT AFTER DISCOUNT:</td>
                            <td>₹{{ formatCurrency(currentPdfData?.amount_after_discount) }}</td>
                        </tr>
                        <tr>
                            <td>GST:</td>
                            <td>₹{{ formatCurrency(currentPdfData?.GST_amount) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td><strong>TOTAL AMOUNT:</strong></td>
                            <td><strong>₹{{ formatCurrency(currentPdfData?.total) }}</strong></td>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="pdf-page">
            <div class="terms" v-if="selectedTermsData?.terms?.length > 0">
                <h4>Terms & Conditions</h4>
                <div class="terms-grid">
                    <div v-for="(term, index) in selectedTermsData.terms" :key="index" class="term-section">
                        <h5>{{ term.title }}</h5>
                        <p>{{ term.description }}</p>
                    </div>
                </div>
            </div>

            <div class="signature">
                <p>For any queries regarding this quotation, please contact us at info@nyife.chat</p>
                <p class="signature-line">____________________</p>
                <p class="name">{{ currentPdfData?.signature }}</p>
                <p class="title">{{ currentPdfData?.designation }}</p>
            </div>

            <p class="absolute text-nowrap bottom-4 left-[50%] -translate-x-[50%] text-xs text-black/50">
                This is an auto-generated quotation and does not require a physical signature.
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import { toast } from 'vue3-toastify';
import FormPhoneInput from './FormPhoneInput.vue';
import FormInput from './FormInput.vue';
import FormDateInput from './FormDateInput.vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const base_url = import.meta.env.VITE_BACKEND_API_URL;
const whatsapp_token = import.meta.env.VITE_WA_TOKEN;

const props = defineProps({
    isOpen: Boolean,
    item: Object
});
const emit = defineEmits(['close', 'success']);

const Designations = ref(["Business Manager", "Sales Agent", "Customer Support Agent", "Marketing Agent", "Finance Agent"]);
const activePlans = ref([]);
const termsAndConditionsList = ref([]);
const isLoadingTerms = ref(false);
const products = ref([]);
const categories = ref(["Good", "Service"]);
const isLoadingProducts = ref(false);
const selectedCategory = ref('');
const productSearchQuery = ref('');

const pdfTemplate = ref(null);
const isSubmitting = ref(false);
const showShareOptions = ref(false);
const currentPdfData = ref(null);

const isSharing = ref({
    whatsapp: false,
    freeWhatsapp: false,
    email: false,
    downloading: false
});

const gstState = ref({ state: "Initial", message: "" });

const formData = ref({
    companyName: '',
    contactPerson: '',
    phone: '',
    email: '',
    address: '',
    gst_number: '',
    selectedPlanId: null,
    platformChargeType: '',
    platformCharge: 0,
    serviceChargeHsnSac: '998314',
    serviceChargeGstRate: 18,
    walletRecharge: 0,
    setupFee: 0,
    customizationFee: 0,
    discount: 0,
    quotation_valid_until_date: '',
    signature: '',
    designation: Designations.value[0],
    termsAndConditions: '',
});

const errors = ref({});
const additionalItems = ref([]);

// Pre-fill form when item changes
watch(() => props.item, (newItem) => {
    if (newItem) {
        prefillForm(newItem);
    }
}, { immediate: true });

watch(() => props.isOpen, (val) => {
    if (val && props.item) {
        prefillForm(props.item);
        fetchSubscriptionPlans();
        fetchTermsAndConditions();
        fetchProducts();
    }
});

function prefillForm(item) {
    formData.value = {
        companyName: item.company_name || '',
        contactPerson: item.contact_person || '',
        phone: item.phone || '',
        email: item.email || '',
        address: item.address || '',
        gst_number: item.gst_number || '',
        selectedPlanId: item.selected_plan_id || null,
        platformChargeType: item.platform_charge_type || '',
        platformCharge: item.platform_charge || 0,
        serviceChargeHsnSac: item.service_charge_hsn_sac || '998314',
        serviceChargeGstRate: item.GST || 18,
        walletRecharge: item.wallet_recharge || 0,
        setupFee: item.setup_fee || 0,
        customizationFee: item.customization_fee || 0,
        discount: item.discount || 0,
        quotation_valid_until_date: item.quotation_valid_until_date || '',
        signature: item.signature || page?.props?.auth?.user?.full_name || page?.props?.auth?.user?.first_name || '',
        designation: item.designation || Designations.value[0],
        termsAndConditions: item.terms_conditions_id || '',
    };

    // Pre-fill additional items
    if (item.additional_fee && item.additional_fee.length > 0) {
        additionalItems.value = item.additional_fee.map(f => ({
            productId: f.product_id || null,
            description: f.description || '',
            hsn_sac: f.hsn_sac || '',
            quantity: f.quantity || 1,
            unit_price: f.unit_price || 0,
            discount: f.discount || 0,
            gst_rate: f.gst_rate || 18,
            base_amount: f.base_amount || 0,
            discount_amount: f.discount_amount || 0,
            gst_amount: f.gst_amount || 0,
            amount: f.amount || 0,
        }));
    } else {
        additionalItems.value = [];
    }
}

const createNewItem = () => ({
    productId: null,
    description: '',
    quantity: 1,
    unit_price: 0,
    discount: 0,
    hsn_sac: '',
    gst_rate: 18,
    base_amount: 0,
    discount_amount: 0,
    gst_amount: 0,
    amount: 0
});

const fetchSubscriptionPlans = async () => {
    try {
        const res = await axios.get(`${base_url}/subscription-plans`);
        if (!res?.data?.success) {
            throw new Error(res?.data?.message || 'Failed to fetch subscription plans');
        }
        activePlans.value = res.data.data || [];
    } catch (e) {
        console.error('Failed to fetch subscription plans', e);
    }
};



const fetchTermsAndConditions = async () => {
    isLoadingTerms.value = true;
    try {
        const result = await axios.get(`${base_url}/terms-conditions/quotation`);
        if (result.data.success && result.data?.data?.data?.length > 0) {
            termsAndConditionsList.value = result.data.data.data;
            if (!formData.value.termsAndConditions) {
                const primary = termsAndConditionsList.value.find(t => t.is_primary);
                if (primary) formData.value.termsAndConditions = primary.id;
            }
        }
    } catch (e) {
        console.error('Failed to fetch terms', e);
    } finally {
        isLoadingTerms.value = false;
    }
};

const fetchProducts = async () => {
    isLoadingProducts.value = true;
    try {
        const params = new URLSearchParams();
        if (selectedCategory.value) params.append('category', selectedCategory.value);
        if (productSearchQuery.value) params.append('search', productSearchQuery.value);
        const response = await axios.get(`${base_url}/products?${params.toString()}`);
        if (response.data.success) {
            products.value = response.data.data.data || [];
        }
    } catch (e) {
        console.error('Failed to fetch products', e);
    } finally {
        isLoadingProducts.value = false;
    }
};

const verifyGst = async () => {
    gstState.value = { state: "Loading", message: "Verifying... (may take up to 1 minute)" };
    try {
        const res = await axios.post(`${base_url}/gst/verify`, { gst_number: formData.value.gst_number });
        if (res.data.success) {
            formData.value.address = res.data.data.gst_data.Principal_Place_of_Business;
            formData.value.companyName = res.data.data.gst_data.LegalName_of_Busines;
            gstState.value = { state: "Success", message: res.data.message || "GST Verified" };
        } else {
            throw new Error(res.data.message || "Verification failed");
        }
    } catch (err) {
        gstState.value = { state: "Error", message: err.message || "Verification failed" };
    }
};

const updatePlatformChargeDetails = () => {
    const plan = activePlans.value.find(p => p.id === formData.value.selectedPlanId);
    if (plan) {
        formData.value.platformCharge = plan.price;
        formData.value.platformChargeType = plan.name;
    } else {
        formData.value.platformCharge = 0;
        formData.value.platformChargeType = '';
    }
};

const handleProductSelect = (index, productId) => {
    const product = products.value.find(p => p.id === productId);
    if (product) {
        additionalItems.value[index].description = product.name;
        additionalItems.value[index].hsn_sac = product.hsn_sac || '';
        additionalItems.value[index].unit_price = product.amount || 0;
        additionalItems.value[index].gst_rate = product.gst_rate || 18;
        additionalItems.value[index].quantity = 1;
        handleQuantityChange(index);
    }
};

const handleQuantityChange = (index) => {
    const item = additionalItems.value[index];
    const qty = Number(item.quantity) || 1;
    const price = Number(item.unit_price) || 0;
    const discount = Number(item.discount) || 0;
    const gstRate = Number(item.gst_rate) || 0;

    const base_amount = qty * price;
    const discount_amount = Math.round(base_amount * discount) / 100;
    const discounted = base_amount - discount_amount;
    const gst_amount = Math.round(discounted * gstRate) / 100;
    const total = Math.round((discounted + gst_amount) * 100) / 100;

    item.base_amount = base_amount;
    item.discount_amount = discount_amount;
    item.gst_amount = gst_amount;
    item.amount = total;
};

const addAdditionalItem = () => {
    additionalItems.value.push(createNewItem());
};

const removeAdditionalItem = (index) => {
    additionalItems.value.splice(index, 1);
};

const formatCurrency = (value) => {
    const num = parseFloat(value) || 0;
    return num.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const calculateSubtotal = () => {
    let total = 0;
    if (formData.value.platformCharge) total += parseFloat(formData.value.platformCharge) || 0;
    if (formData.value.walletRecharge) total += parseFloat(formData.value.walletRecharge) || 0;
    if (formData.value.setupFee) total += parseFloat(formData.value.setupFee) || 0;
    if (formData.value.customizationFee) total += parseFloat(formData.value.customizationFee) || 0;

    total += additionalItems.value.reduce((acc, item) => {
        return acc + Number(item.base_amount ?? 0);
    }, 0);

    return total;
};

const calculateDiscount = () => {
    const discount = parseFloat(formData.value.discount) || 0;

    let serviceTotal = 0;
    if (formData.value.platformCharge) serviceTotal += parseFloat(formData.value.platformCharge) || 0;
    if (formData.value.walletRecharge) serviceTotal += parseFloat(formData.value.walletRecharge) || 0;
    if (formData.value.setupFee) serviceTotal += parseFloat(formData.value.setupFee) || 0;
    if (formData.value.customizationFee) serviceTotal += parseFloat(formData.value.customizationFee) || 0;

    const additionalItemsDiscount = additionalItems.value.reduce((acc, item) => {
        return acc + Number(item.discount_amount ?? 0);
    }, 0);

    return (serviceTotal * (discount / 100)) + (additionalItemsDiscount || 0);
};

const calculateAmountAfterDiscount = () => calculateSubtotal() - calculateDiscount();

const calculateGST = () => {
    const toNumber = (v) => Number(v) || 0;

    const totalServiceAmount =
        toNumber(formData.value.platformCharge) +
        toNumber(formData.value.walletRecharge) +
        toNumber(formData.value.setupFee) +
        toNumber(formData.value.customizationFee);

    const discountRate = toNumber(formData.value.discount);
    const discountedServiceAmount = totalServiceAmount * (1 - discountRate / 100);

    const serviceGstRate = toNumber(formData.value.serviceChargeGstRate);
    const serviceGst = discountedServiceAmount * (serviceGstRate / 100);

    const additionalGst = additionalItems.value.reduce(
        (sum, item) => sum + toNumber(item.gst_amount),
        0
    );

    return Number((serviceGst + additionalGst).toFixed(2));
};

const calculateTotal = () => calculateAmountAfterDiscount() + calculateGST();

const selectedTermsData = computed(() => {
    const selected = termsAndConditionsList.value.find(t => t.id === formData.value.termsAndConditions);
    return selected || null;
});

const getVisibleItems = (Data, additionalData) => {
    const items = [];
    const discount = Number(Data?.discount ?? 0);
    const gst_rate = Number(Data?.GST ?? 0);

    if (parseFloat(Data?.platform_charge) > 0) {
        const unit_price = Number(Data?.platform_charge ?? 0);
        const discount_amount = Math.round(unit_price * discount) / 100;
        const discounted_price = unit_price - discount_amount;
        const gst_amount = Math.round(discounted_price * gst_rate) / 100;
        items.push({
            description: `Platform Charge (${Data?.platform_charge_type})`,
            quantity: 1, unit_price, discount, amount: Math.round((discounted_price + gst_amount) * 100) / 100,
            hsn_sac: Data?.service_charge_hsn_sac || '998314', gst_rate, discount_amount, gst_amount,
        });
    }
    if (parseFloat(Data?.wallet_recharge) > 0) {
        const unit_price = Number(Data?.wallet_recharge ?? 0);
        const discount_amount = Math.round(unit_price * discount) / 100;
        const discounted_price = unit_price - discount_amount;
        const gst_amount = Math.round(discounted_price * gst_rate) / 100;
        items.push({
            description: 'Wallet Recharge', quantity: 1, unit_price, discount,
            amount: Math.round((discounted_price + gst_amount) * 100) / 100,
            hsn_sac: Data?.service_charge_hsn_sac || '998314', gst_rate, discount_amount, gst_amount,
        });
    }
    if (parseFloat(Data?.setup_fee) > 0) {
        const unit_price = Number(Data?.setup_fee ?? 0);
        const discount_amount = Math.round(unit_price * discount) / 100;
        const discounted_price = unit_price - discount_amount;
        const gst_amount = Math.round(discounted_price * gst_rate) / 100;
        items.push({
            description: 'Setup Fee', quantity: 1, unit_price, discount,
            amount: Math.round((discounted_price + gst_amount) * 100) / 100,
            hsn_sac: Data?.service_charge_hsn_sac || '998314', gst_rate, discount_amount, gst_amount,
        });
    }
    if (parseFloat(Data?.customization_fee) > 0) {
        const unit_price = Number(Data?.customization_fee ?? 0);
        const discount_amount = Math.round(unit_price * discount) / 100;
        const discounted_price = unit_price - discount_amount;
        const gst_amount = Math.round(discounted_price * gst_rate) / 100;
        items.push({
            description: 'Customization Fee', quantity: 1, unit_price, discount,
            amount: Math.round((discounted_price + gst_amount) * 100) / 100,
            hsn_sac: Data?.service_charge_hsn_sac || '998314', gst_rate, discount_amount, gst_amount,
        });
    }
    if (Array.isArray(additionalData)) {
        additionalData.forEach(item => items.push(item));
    }
    return items;
};

const validateForm = () => {
    const newErrors = {};
    if (!formData.value.contactPerson?.trim()) newErrors.contactPerson = 'Contact person is required';
    if (!formData.value.email?.trim()) newErrors.email = 'Email is required';
    if (!formData.value.phone?.trim()) newErrors.phone = 'Phone is required';
    if (!formData.value.companyName?.trim() && !formData.value.gst_number?.trim()) newErrors.companyName = 'Company name or GST is required';
    if (!formData.value.quotation_valid_until_date) newErrors.quotation_valid_until_date = 'Valid until date is required';
    if (!formData.value.signature?.trim()) newErrors.signature = 'Signature is required';
    if (!formData.value.designation) newErrors.designation = 'Designation is required';
    errors.value = newErrors;
    if (Object.keys(newErrors).length > 0) {
        toast.error('Please fill in all required fields');
        return false;
    }
    return true;
};

const generatePDFBlob = async () => {
    const PDF_CONFIG = {
        A4_WIDTH_MM: 210, A4_HEIGHT_MM: 297,
        A4_WIDTH_PX: 793.7, PADDING_MM: 10,
        CANVAS_SCALE: 4, IMAGE_QUALITY: 0.95, RENDER_DELAY_MS: 200
    };
    try {
        await new Promise(resolve => setTimeout(resolve, PDF_CONFIG.RENDER_DELAY_MS));
        const element = pdfTemplate.value;
        if (!element) throw new Error('PDF template element not found');
        const firstPageContent = element.querySelector('.pdf-content > div:first-child');
        const secondPageContent = element.querySelector('.pdf-content > div:last-child');
        if (!firstPageContent || !secondPageContent) throw new Error('Required content sections not found');
        if (!currentPdfData?.value) throw new Error('Quotation data is not available');
        const createPageElement = (content) => {
            const pageDiv = document.createElement('div');
            Object.assign(pageDiv.style, {
                width: `${PDF_CONFIG.A4_WIDTH_MM}mm`, minHeight: `${PDF_CONFIG.A4_HEIGHT_MM}mm`,
                padding: `${PDF_CONFIG.PADDING_MM}mm`, backgroundColor: '#ffffff',
                boxSizing: 'border-box', position: 'relative',
                fontSmoothing: 'antialiased', WebkitFontSmoothing: 'antialiased'
            });
            pageDiv.appendChild(content.cloneNode(true));
            return pageDiv;
        };
        const tempContainer = document.createElement('div');
        Object.assign(tempContainer.style, {
            position: 'fixed', left: '0', top: '0', zIndex: '-1000', opacity: '0', pointerEvents: 'none'
        });
        document.body.appendChild(tempContainer);
        const canvasConfig = {
            scale: PDF_CONFIG.CANVAS_SCALE, useCORS: true, allowTaint: false,
            logging: false, backgroundColor: '#ffffff', width: PDF_CONFIG.A4_WIDTH_PX,
            windowWidth: PDF_CONFIG.A4_WIDTH_PX, imageTimeout: 15000, removeContainer: false
        };
        const firstPage = createPageElement(firstPageContent);
        tempContainer.appendChild(firstPage);
        const canvas1 = await html2canvas(firstPage, canvasConfig);
        tempContainer.innerHTML = '';
        const secondPage = createPageElement(secondPageContent);
        tempContainer.appendChild(secondPage);
        const canvas2 = await html2canvas(secondPage, canvasConfig);
        document.body.removeChild(tempContainer);
        const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4', compress: true, hotfixes: ['px_scaling'] });
        const addImageToPDF = (canvas, pageIndex = 0) => {
            const imgData = canvas.toDataURL('image/jpeg', PDF_CONFIG.IMAGE_QUALITY);
            const imgWidth = PDF_CONFIG.A4_WIDTH_MM;
            const imgHeight = (canvas.height * PDF_CONFIG.A4_WIDTH_MM) / canvas.width;
            const finalHeight = Math.min(imgHeight, PDF_CONFIG.A4_HEIGHT_MM);
            if (pageIndex > 0) pdf.addPage();
            pdf.addImage(imgData, 'JPEG', 0, 0, imgWidth, finalHeight, undefined, 'FAST');
        };
        addImageToPDF(canvas1, 0);
        addImageToPDF(canvas2, 1);
        const sanitize = (str) => str ? str.replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 100) : 'Unknown';
        const fileName = `Quotation_${sanitize(currentPdfData?.value?.quotation_number || 'N/A')}_${sanitize(currentPdfData?.value?.company_name || 'Company')}.pdf`;
        return { blob: pdf.output('blob'), pdf, fileName };
    } catch (error) {
        throw new Error(`Failed to generate PDF: ${error.message}`);
    }
};



const handleSubmit = async () => {
    if (!validateForm()) return;
    isSubmitting.value = true;

    try {
        const payload = {
            company_name: formData.value.companyName,
            contact_person: formData.value.contactPerson,
            gst_number: formData.value.gst_number || null,
            phone: formData.value.phone,
            email: formData.value.email,
            address: formData.value.address,
            selected_plan_id: formData.value.selectedPlanId,
            platform_charge_type: formData.value.platformChargeType,
            platform_charge: formData.value.platformCharge,
            service_charge_hsn_sac: formData.value.serviceChargeHsnSac,
            GST: formData.value.serviceChargeGstRate,
            wallet_recharge: formData.value.walletRecharge,
            setup_fee: formData.value.setupFee,
            customization_fee: formData.value.customizationFee,
            additional_fee: additionalItems.value.map(item => ({
                product_id: item.productId,
                description: item.description,
                hsn_sac: item.hsn_sac,
                unit_price: item.unit_price,
                quantity: item.quantity,
                base_amount: item.base_amount,
                discount: item.discount,
                discount_amount: item.discount_amount,
                gst_rate: item.gst_rate,
                gst_amount: item.gst_amount,
                amount: item.amount,
            })),
            discount: formData.value.discount,
            quotation_valid_until_date: formData.value.quotation_valid_until_date,
            signature: formData.value.signature,
            designation: formData.value.designation,
            created_by: page?.props?.auth?.user?.id || null,
        };

        const res = await axios.put(`${base_url}/invoices/update-quotation/${props.item.id}`, payload);

        if (!res?.data?.success) {
            throw new Error(res?.data?.message || 'Failed to update quotation');
        }

        currentPdfData.value = res?.data?.data;

        const { blob, fileName } = await generatePDFBlob();

        const formDataUpload = new FormData();
        formDataUpload.append("pdf_data", blob, fileName);
        formDataUpload.append("pdf_type", "quotation");
        formDataUpload.append("id", res?.data?.data.id);

        const uploadRes = await axios.post(`${base_url}/uploads`, formDataUpload, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        if (!uploadRes?.data?.success) {
            throw new Error(uploadRes?.data?.message || 'Failed to upload quotation PDF');
        }

        currentPdfData.value.quotation_invoice_pdf_url = uploadRes.data.data.file.url;
        currentPdfData.value.quotation_invoice_pdf_download_url = uploadRes.data.data.file.downloadUrl;
        currentPdfData.value.quotation_invoice_pdf_filename = uploadRes.data.data.file.filename;

        toast.success('Quotation updated successfully!');
        closeModal();

        emit('success');

        setTimeout(() => {
            showShareOptions.value = true;
        }, 300);

    } catch (error) {
        toast.error(error.message || 'Error updating quotation. Please try again.');
    } finally {
        isSubmitting.value = false;
    }
};

const closeModal = () => {
    emit('close');
};

const closeShareModal = () => {
    showShareOptions.value = false;
    currentPdfData.value = null;
};

const downloadPDF = async () => {
    if (!currentPdfData?.value?.quotation_invoice_pdf_download_url) {
        toast.error('No PDF available to download');
        return;
    }

    isSharing.value.downloading = true;

    try {
        const response = await axios.get(
            currentPdfData?.value?.quotation_invoice_pdf_download_url,
            { responseType: "blob" }
        );

        const blob = response.data;
        const blobUrl = window.URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.href = blobUrl;
        link.download = currentPdfData?.value?.quotation_invoice_pdf_filename || "quotation.pdf";
        document.body.appendChild(link);
        link.click();

        document.body.removeChild(link);
        window.URL.revokeObjectURL(blobUrl);

        toast.success('PDF downloaded successfully!');
    } catch (error) {
        console.error('Download error:', error);
        toast.error('Error downloading PDF. Please try again.');
    } finally {
        isSharing.value.downloading = false;
    }
};

const shareOnFreeWhatsApp = async () => {
    if (!currentPdfData?.value?.quotation_invoice_pdf_url) {
        toast.error('No PDF available to share');
        return;
    }

    if (!currentPdfData?.value?.phone) {
        toast.error('Phone number is required');
        return;
    }

    isSharing.value.freeWhatsapp = true;

    try {
        const response = await fetch("https://wa.nyife.chat/api/send/media", {
            method: "POST",
            headers: {
                "Authorization": `Bearer ${whatsapp_token}`,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                phone: currentPdfData?.value?.phone,
                media_type: "document",
                media_url: currentPdfData?.value?.quotation_invoice_pdf_url,
                file_name: currentPdfData?.value?.quotation_invoice_pdf_filename,
                caption: `Dear ${currentPdfData?.value?.contact_person || 'Sir'},

Thank you for your interest! Please download your quotation.

If you have any questions, feel free to reply here.

Looking forward to assisting you.`,
            })
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => null);
            console.error("WhatsApp API Error:", errorData);
            throw new Error("Failed to share on WhatsApp");
        }

        toast.success("Quotation shared on WhatsApp!");

    } catch (error) {
        console.error("Error sharing on WhatsApp:", error);
        toast.error("Error sharing on WhatsApp. Please try again.");
    } finally {
        isSharing.value.freeWhatsapp = false;
    }
};

const shareOnWhatsApp = async () => {
    if (!currentPdfData?.value?.quotation_invoice_pdf_url) {
        toast.error('No PDF available to share');
        return;
    }

    if (!currentPdfData?.value?.phone) {
        toast.error('Phone number is required');
        return;
    }

    isSharing.value.whatsapp = true;

    try {
        const url = currentPdfData?.value?.quotation_invoice_pdf_url;
        const fileName = currentPdfData?.value?.quotation_invoice_pdf_filename;
        const response = await fetch("https://wa.nyife.chat/api/send/template", {
            method: "POST",
            headers: {
                "Authorization": `Bearer ${whatsapp_token}`,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                phone: currentPdfData?.value?.phone,
                template: {
                    name: "quotation_invoice",
                    language: { code: "en" },
                    components: [
                        {
                            type: "header",
                            parameters: [
                                {
                                    type: "document",
                                    document: {
                                        link: url,
                                        filename: fileName || "Quotation.pdf"
                                    }
                                }
                            ]
                        }
                    ]
                }
            })
        });

        const data = await response.json().catch(() => null);

        if (data.data.success === false) {
            throw new Error(data.data.message || "Failed to share on WhatsApp");
        }

        toast.success("Quotation shared on WhatsApp!");

    } catch (error) {
        toast.error(error.message || 'Error sharing on WhatsApp. Please try again.');
    } finally {
        isSharing.value.whatsapp = false;
    }
};

const shareViaEmail = async () => {
    if (!currentPdfData?.value?.quotation_invoice_pdf_url) {
        toast.error('No PDF available to share');
        return;
    }

    if (!formData?.value?.email) {
        toast.error('Email address is required');
        return;
    }

    isSharing.value.email = true;

    try {
        const payload = {
            customer_name: currentPdfData?.value?.contact_person,
            invoice_type: 'Quotation',
            invoice_number: currentPdfData?.value?.quotation_number,
            invoice_url: currentPdfData?.value?.quotation_invoice_pdf_download_url,
            email: currentPdfData?.value?.email
        };

        const response = await axios.post(`${base_url}/email/share-invoice`, payload);

        if (!response?.data?.success) {
            throw new Error('Failed to send email');
        }

        toast.success(`Quotation sent to ${currentPdfData?.value?.email} successfully!`);

    } catch (error) {
        toast.error('Error sending email. Please try again.');
    } finally {
        isSharing.value.email = false;
    }
};
</script>

<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 1rem;
}

.modal-container {
    background: white;
    border-radius: 1rem;
    max-width: 800px;
    width: 100%;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.modal-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
}

.close-btn {
    padding: 0.5rem;
    border-radius: 0.5rem;
    transition: background-color 0.2s;
    border: none;
    background: transparent;
    cursor: pointer;
}

.close-btn:hover {
    background-color: #f3f4f6;
}

.modal-body {
    padding: 1.5rem;
    overflow-y: auto;
}

.subtitle {
    color: #6b7280;
    margin-bottom: 1.5rem;
    font-size: 0.875rem;
}

/* Form Grid */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

/* Form Group */
.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 12px 14px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
    width: 100%;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #ff5100;
    box-shadow: 0 0 0 3px rgba(255, 81, 0, 0.1);
}

.error-input {
    border-color: #e53e3e !important;
}

.error-text {
    color: #e53e3e;
    font-size: 12px;
    margin-top: 4px;
}

.required {
    color: #e53e3e;
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin: 30px 0 15px 0;
    padding-bottom: 10px;
    border-bottom: 2px solid #ff5100;
}

.section-title.no-border {
    margin: 0;
    border: none;
    padding: 0;
}

.additional-items {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    margin-top: 20px;
}

.additional-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.full-width {
    grid-column: 1 / -1;
}


.item-row-extended {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}


.item-row-extended .btn-danger {
    margin-top: 1.25rem;
    width: 100%;
    justify-content: center;
}

.item-details-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-top: 12px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-primary {
    background: #ff5100;
    color: white;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 81, 0, 0.4);
}

.btn-outline {
    background: white;
    color: #374151;
    border: 2px solid #e2e8f0;
}

.btn-outline:hover {
    background: #f9fafb;
}

.btn-secondary {
    background: #48bb78;
    color: white;
}

.btn-secondary:hover:not(:disabled) {
    background: #38a169;
}

.btn-danger {
    background: #f56565;
    color: white;
    padding: 8px 12px;
    margin-top: 1.5rem;
}

.btn-danger:hover {
    background: #e53e3e;
}

.btn-group {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    justify-content: center;
}

.summary-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-top: 20px;
}

.summary-title {
    margin-bottom: 15px;
    color: #333;
    font-size: 18px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 15px;
}

.summary-row.total {
    font-size: 20px;
    font-weight: 700;
    color: #ff5100;
    border-top: 2px solid #ddd;
    padding-top: 15px;
    margin-top: 10px;
}

/* Share Modal */
.share-modal {
    background: white;
    border-radius: 1rem;
    max-width: 500px;
    width: 100%;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.share-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.share-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
}

.share-body {
    padding: 1.5rem;
}

.share-subtitle {
    color: #6b7280;
    margin-bottom: 1.5rem;
    font-size: 0.95rem;
}

.share-options {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.share-option {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.75rem;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: left;
}

.share-option:hover:not(:disabled) {
    border-color: #ff5100;
    background: #fff5f0;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 81, 0, 0.1);
}

.share-option:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.share-icon-wrapper {
    width: 56px;
    height: 56px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.whatsapp-bg {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color: white;
}

.email-bg {
    background: linear-gradient(135deg, #EA4335 0%, #C5221F 100%);
    color: white;
}

.download-bg {
    background: linear-gradient(135deg, #4285F4 0%, #1967D2 100%);
    color: white;
}

.share-content {
    flex: 1;
}

.share-content h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
}

.share-content p {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
}

/* PDF Template Styles */
#uq-pdf-template {
    position: absolute;
    left: -9999px;
    display: none;
    width: 793.7px;
}

.pdf-page {
    background-image: url("../../images/nyife-bg.png");
    background-size: 70%;
    background-position: center;
    background-repeat: no-repeat;
    height: 297mm;
}

.pdf-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #333;
}

.company-info h2 {
    color: #ff5100;
    font-size: 24px;
    margin-bottom: 5px;
}

.company-info p {
    font-size: 11px;
    color: #666;
    margin: 2px 0;
}

.invoice-info {
    text-align: right;
    background: linear-gradient(135deg, #ff5100 0%, #ff7d47 100%);
    padding: 16px 20px;
    border-radius: 8px;
    color: white;
    height: fit-content;
    margin-top: auto;
}

.invoice-info h3 {
    font-size: 20px;
    color: white;
    margin-bottom: 8px;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.invoice-info p {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.95);
    margin: 4px 0;
}

.client-info {
    background: #f8f9fa;
    padding: 16px;
    margin-bottom: 25px;
    border-radius: 5px;
    border-left: 4px solid #ff5100;
}

.client-info h4 {
    font-size: 13px;
    color: #ff5100;
    margin-bottom: 8px;
    font-weight: 700;
}

.client-info p {
    font-size: 12px;
    color: #555;
    line-height: 1.6;
    margin: 2px 0;
}

.thank-you-note {
    padding: 15px;
    background: #ff51002d;
    border-radius: 5px;
    border-left: 4px solid #ff5100;
    margin-bottom: 25px;
}

.thank-you-note p {
    margin: 0;
    color: #333;
    font-size: 11px;
    line-height: 1.6;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 25px;
}

.items-table th {
    background: #ff5100;
    color: white;
    padding: 12px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
}

.items-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #e0e0e0;
    font-size: 12px;
    color: #333;
}

.items-table tr:last-child td {
    border-bottom: none;
}

.summary-section {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}

.summary-table-wrapper {
    flex: 1;
}

.summary-table {
    width: 100%;
    border-collapse: collapse;
}

.summary-table tr {
    border-bottom: 1px solid #e0e0e0;
}

.summary-table td {
    padding: 10px 12px;
    font-size: 13px;
}

.summary-table td:first-child {
    color: #666;
}

.summary-table td:last-child {
    text-align: right;
    font-weight: 600;
    color: #333;
}

.summary-table .discount-amount {
    color: #28a745;
}

.summary-table .total-row {
    background: linear-gradient(135deg, #ff5100 0%, #ff7d47 100%);
}

.summary-table .total-row td {
    padding: 14px 12px;
    font-size: 16px;
    border: none;
    color: white;
}

.terms {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid #333;
}

.terms h4 {
    font-size: 14px;
    color: #333;
    margin-bottom: 12px;
}

.terms-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.term-section h5 {
    font-size: 11px;
    color: #ff5100;
    margin-bottom: 5px;
    font-weight: 700;
}

.term-section p {
    font-size: 10px;
    color: #555;
    line-height: 1.5;
    margin-bottom: 8px;
}

.signature {
    margin: 100px 0 60px 0;
    text-align: right;
}

.signature p {
    font-size: 12px;
    color: #333;
    margin: 5px 0;
}

.signature .signature-line {
    margin-top: 30px;
    margin-bottom: 5px;
}

.signature .name {
    font-weight: 700;
    font-size: 14px;
    color: #ff5100;
}

.signature .title {
    font-size: 11px;
    color: #666;
}
</style>
