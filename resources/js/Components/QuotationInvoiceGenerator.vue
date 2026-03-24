<template>
    <div class="">
        <!-- Trigger Button -->
        <div class="flex justify-center">
            <button @click="openModal"
                class="bg-primary text-white font-semibold px-4 md:px-6 py-2.5 rounded-xl transition-all duration-300 hover:scale-[1.02] shadow-lg flex items-center gap-2">
                <CirclePlus />
                Create Quotation
            </button>
        </div>

        <!-- FORM Modal -->
        <div v-if="isModalOpen" class="modal-overlay" @click.self="closeModal">
            <div class="modal-container">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h1 class="modal-title">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" class="text-primary"
                            stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                        Create & Share Quotation
                    </h1>
                    <button @click="closeModal" class="close-btn">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">

                    <div class="user-type-selector">
                        <label class="section-title">Select User Type</label>
                        <div class="radio-group pt-2">
                            <label class="radio-option" :class="{ 'active': userType === 'existing' }">
                                <input type="radio" v-model="userType" value="existing">
                                <div class="radio-content">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                    <span>Existing User</span>
                                </div>
                            </label>
                            <label class="radio-option" :class="{ 'active': userType === 'new' }">
                                <input type="radio" v-model="userType" value="new">
                                <div class="radio-content">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="8.5" cy="7" r="4"></circle>
                                        <line x1="20" y1="8" x2="20" y2="14"></line>
                                        <line x1="23" y1="11" x2="17" y2="11"></line>
                                    </svg>
                                    <span>New User</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- ==================== EXISTING USER SELECTION (NEW) ==================== -->
                    <div v-if="userType === 'existing'" class="user-selection-section">
                        <div class="section-title">Select User</div>

                        <!-- Search and Filter -->
                        <div class="search-filter-container">
                            <div class="search-box">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.35-4.35"></path>
                                </svg>
                                <input type="text" v-model="searchQuery" placeholder="Search by name, email or phone..."
                                    @input="debouncedSearch">
                                <button v-if="searchQuery" @click="clearSearch" class="clear-btn">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Selected User Info -->
                        <div v-if="selectedUser" class="selected-user-card">
                            <div class="selected-user-header">
                                <span class="selected-label">Selected User:</span>
                                <button @click="clearUserSelection" class="change-btn">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <polyline points="1 4 1 10 7 10"></polyline>
                                        <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                                    </svg>
                                    Change
                                </button>
                            </div>
                            <div class="selected-user-info">
                                <div class="user-avatar">{{ getInitials(selectedUser.first_name, selectedUser.last_name)
                                    }}</div>
                                <div class="user-details">
                                    <div class="user-name">{{ selectedUser.first_name }} {{ selectedUser.last_name || ''
                                        }}</div>
                                    <div class="user-meta">{{ selectedUser.email }}</div>
                                    <div class="user-meta">{{ selectedUser.phone }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Users List -->
                        <div v-if="!selectedUser" class="users-list-container">
                            <div v-if="isLoadingUsers" class="loading-state">
                                <div class="spinner"></div>
                                <span>Loading users...</span>
                            </div>

                            <div v-else-if="users.length === 0" class="empty-state">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.5">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                <p>No users found</p>
                                <span>Try adjusting your search or filters</span>
                            </div>

                            <div v-else class="users-list">
                                <div v-for="user in users" :key="user.id" class="user-card" @click="selectUser(user)">
                                    <div class="user-avatar-small">{{ getInitials(user.first_name, user.last_name) }}
                                    </div>
                                    <div class="user-info">
                                        <div class="user-name-row">
                                            <span class="user-name">{{ user.first_name }} {{ user.last_name || ''
                                                }}</span>
                                            <span class="user-role-badge" :class="`role-${user.role.toLowerCase()}`">{{
                                                user.role }}</span>
                                        </div>
                                        <div class="user-contact">
                                            <span>{{ user.email }}</span>
                                            <span class="separator">•</span>
                                            <span>{{ user.phone }}</span>
                                        </div>
                                    </div>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" class="chevron-icon">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </div>
                            </div>

                            <!-- Pagination -->
                            <div v-if="pagination.totalPages > 1" class="pagination-container">
                                <button @click="goToPage(pagination.currentPage - 1)"
                                    :disabled="pagination.currentPage === 1" class="pagination-btn">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <polyline points="15 18 9 12 15 6"></polyline>
                                    </svg>
                                    Previous
                                </button>

                                <div class="pagination-info">
                                    Page {{ pagination.currentPage }} of {{ pagination.totalPages }}
                                    <span class="total-items">({{ pagination.totalItems }} users)</span>
                                </div>

                                <button @click="goToPage(pagination.currentPage + 1)"
                                    :disabled="pagination.currentPage === pagination.totalPages" class="pagination-btn">
                                    Next
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="userType === 'new' || selectedUser" class="form-section">
                        <p class="subtitle">Fill in the details below to generate your quotation</p>

                        <div class="form-grid">
                            <div class="form-group">

                                <FormInput v-model="formData.contactPerson" :name="$t('contactPerson')"
                                    :label="$t('Contact Person')" :error="errors.contactPerson" type="text"
                                    :required="true" />
                            </div>
                            <div class="form-group">
                                <FormInput v-model="formData.email" :name="$t('Email')" :label="$t('Email')"
                                    :error="errors.email" type="email" :required="true" />
                            </div>
                            <div class="form-group pt-1">
                                <FormPhoneInput v-model="formData.phone" :name="$t('Phone')" :label="$t('Phone')"
                                    :error="errors.phone" :required="true" />
                            </div>

                            <div class="form-group">
                                <FormDateInput v-model="formData.quotation_valid_until_date"
                                    @update:modelValue="handleQuotationDateChange" name="quotation_valid_until_date"
                                    label="Quotation Valid Until" :required="true" placeholder="Select validity date"
                                    :error-message="errors.quotation_valid_until_date"
                                    :default-date="getDefaultDateByMonths(1)" />
                            </div>

                            <div class="form-group">
                                <FormInput v-model="formData.signature" :name="$t('signature')" :label="$t('Signature')"
                                    :error="errors.signature" type="text" :required="true" />
                            </div>

                            <div class="form-group !py-0">
                                <label class="!py-0">Designation <span class="required">*</span></label>
                                <select v-model="formData.designation" class="!py-2.5">
                                    <option value="" disabled>Select designation</option>
                                    <option v-for="designation in Designations" :key="designation" :value="designation">
                                        {{ designation }}
                                    </option>
                                </select>
                                <span v-if="errors.designation" class="error-text">{{ errors.designation }}</span>
                            </div>
                        </div>

                        <div class="rounded-md border bg-slate-50/80 border-slate-300 p-6">
                            <div class="form-grid">
                                <div class="form-group relative"> <label>GST Number</label>
                                    <input type="text" v-model="formData.gst_number" placeholder="Enter GST Number"
                                        maxlength="15" :class="{ 'error-input': errors.gst_number }">
                                    <button @click="verifyGst"
                                        :disabled="gstState.state === 'Loading' || formData.gst_number.length !== 15"
                                        class="absolute right-[5px] top-[35px] font-semibold bg-primary/90 disabled:bg-primary/60 disabled:cursor-not-allowed disabled:scale-100 text-white text-sm py-2 px-4 rounded-md transition-all duration-300 hover:scale-[1.01] shadow-sm flex items-center gap-1">
                                        {{ gstState.state === 'Loading' ? 'Verifying...' : 'Verify GST' }}
                                    </button>

                                    <span v-if="gstState.state === 'Success'" class="text-green-600 text-xs pt-2">{{
                                        gstState.message }}
                                    </span>

                                    <span v-if="gstState.state === 'Loading'" class="text-purple-600 text-xs pt-2">{{
                                        gstState.message }}
                                    </span>

                                    <span v-if="gstState.state === 'Error' || errors.gst_number"
                                        class="text-red-600 text-xs pt-2">{{
                                            gstState.message?.trim() || errors.gst_number }}
                                    </span>

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
                                    <textarea v-model="formData.address" placeholder="Enter complete address"
                                        :class="{ 'error-input': errors.address }"></textarea>
                                    <span v-if="errors.address" class="error-text">{{ errors.address }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- terms & conditions -->

                        <div class="section-title">Terms & Conditions</div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="!py-0">
                                    Terms & Conditions
                                    <span class="required">*</span>
                                </label>
                                <select v-model="formData.termsAndConditions" class="!py-2.5"
                                    :disabled="isLoadingTerms">
                                    <option value="" disabled>
                                        {{ isLoadingTerms ? 'Loading...' : 'Select Terms & Conditions' }}
                                    </option>
                                    <option v-for="terms in termsAndConditionsList" :key="terms.id" :value="terms.id">
                                        {{ terms.name }}{{ terms.is_primary ? ' ⭐' : '' }}
                                    </option>
                                </select>
                                <span v-if="errors.termsAndConditions" class="error-text">
                                    {{ errors.termsAndConditions }}
                                </span>
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
                                <input type="number" v-model="formData.platformCharge" placeholder="0" min="0"
                                    step="0.01" readonly style="background-color: #f9fafb; cursor: not-allowed;">
                            </div>
                            <div class="form-group">
                                <label>Wallet Recharge (₹)</label>
                                <input type="number" v-model="formData.walletRecharge" placeholder="0" min="0"
                                    step="0.01">
                            </div>
                            <div class="form-group">
                                <label>Setup Fee (₹)</label>
                                <input type="number" v-model="formData.setupFee" placeholder="0" min="0" step="0.01">
                            </div>
                            <div class="form-group">
                                <label>Customization Fee (₹)</label>
                                <input type="number" v-model="formData.customizationFee" placeholder="0" min="0"
                                    step="0.01">
                            </div>
                            <!-- NEW: HSN/SAC for Platform Charge -->
                            <div class="form-group">
                                <label>HSN/SAC Code</label>
                                <input type="text" v-model="formData.serviceChargeHsnSac" placeholder="HSN/SAC Code">
                            </div>

                            <!-- NEW: GST Rate for Platform Charge -->
                            <div class="form-group">
                                <label>GST Rate (%)</label>
                                <input type="number" v-model="formData.serviceChargeGstRate" placeholder="18" min="0"
                                    max="100" step="0.01">
                            </div>

                            <div class="form-group">
                                <label>Discount (%)</label>
                                <input type="number" v-model="formData.discount" placeholder="0" min="0" max="100"
                                    step="0.01">
                            </div>
                        </div>

                        <!-- Additional Items with Product Selection -->
                        <div class="additional-items">
                            <div class="additional-header">
                                <div class="section-title no-border">Additional Items / Products (Optional)</div>
                                <button class="btn btn-secondary" :disabled="totalRows === 5"
                                    @click="addAdditionalItem">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Add Item
                                </button>
                            </div>

                            <!-- NEW: Product Search and Category Filter -->
                            <div class="product-filters" v-if="additionalItems.length > 0">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Search Products</label>
                                        <input type="text" v-model="productSearchQuery" @input="fetchProducts"
                                            placeholder="Search by name, catgeory or hsn/sac....">
                                    </div>
                                    <div class="form-group">
                                        <label>Filter by Category</label>
                                        <select v-model="selectedCategory" @change="fetchProducts">
                                            <option value="">All Categories</option>
                                            <option v-for="category in categories" :key="category" :value="category">
                                                {{ category }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Updated Item Rows with Product Selection -->
                            <div v-for="(item, index) in additionalItems" :key="index" class="item-row-extended">
                                <!-- Product Selection Dropdown -->
                                <div class="form-group full-width">
                                    <label>Select Product (Optional - or enter custom details below)</label>
                                    <select v-model="item.productId"
                                        @change="handleProductSelect(index, item.productId)"
                                        :disabled="isLoadingProducts">
                                        <option value="">{{ isLoadingProducts ? 'Loading products...' : `Select a
                                            product or enter custom` }}</option>
                                        <option v-for="product in products" :key="product.id" :value="product.id">
                                            {{ product.name }} - {{ product.category }} - ₹{{
                                                formatCurrency(product.amount) }}
                                            (HSN: {{ product.hsn_sac }}, GST: {{ product.gst_rate }}%)
                                        </option>
                                    </select>
                                </div>

                                <!-- Item Details Grid -->
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
                                        <input type="number" v-model="item.quantity"
                                            @input="handleQuantityChange(index)" placeholder="1" min="1" step="1">
                                    </div>

                                    <div class="form-group">
                                        <label>Unit Price (₹)</label>
                                        <input type="number" v-model="item.unit_price"
                                            @input="handleQuantityChange(index)" placeholder="0" min="0" step="0.01">
                                    </div>

                                    <!-- 🆕 NEW: Item-level Discount Field -->
                                    <div class="form-group">
                                        <label>Discount (%)</label>
                                        <input type="number" v-model="item.discount"
                                            @input="handleQuantityChange(index)" placeholder="0" min="0" max="100"
                                            step="0.01">
                                    </div>

                                    <div class="form-group">
                                        <label>GST Rate (%)</label>
                                        <input type="number" v-model="item.gst_rate"
                                            @input="handleQuantityChange(index)" placeholder="18" min="0" max="100"
                                            step="0.01">
                                    </div>

                                    <div class="form-group">
                                        <label>Total Amount (₹)</label>
                                        <input type="number" v-model="item.amount" placeholder="0" min="0" step="0.01"
                                            readonly style="background-color: #f9fafb; cursor: not-allowed;">
                                        <span class="text-xs text-gray-500 mt-1">
                                            Auto-calculated: (Qty × Price) - Discount + GST
                                        </span>
                                    </div>
                                </div>

                                <!-- Remove Button -->
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
                            <button class="btn btn-outline" @click="closeModal">
                                Cancel
                            </button>
                            <button class="btn btn-primary" @click="generatePDF" :disabled="isGenerating">
                                <FileText v-if="!isGenerating" />
                                <span v-if="isGenerating">Generating...</span>
                                <span v-else>Generate PDF</span>
                            </button>
                        </div>
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
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                <div class="share-body">
                    <p class="share-subtitle">Choose how you want to share this quotation</p>

                    <div class="share-options">
                        <!-- Share on Free WhatsApp -->
                        <button @click="shareOnFreeWhatsApp" :disabled="isSharing.freeWhatsapp"
                            class="share-option whatsapp">
                            <div class="share-icon-wrapper whatsapp-bg">
                                <svg v-if="!isSharing.freeWhatsapp" width="28" height="28" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
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

                        <!-- WhatsApp Share -->
                        <button @click="shareOnWhatsApp" :disabled="isSharing.whatsapp" class="share-option whatsapp">
                            <div class="share-icon-wrapper whatsapp-bg">
                                <svg v-if="!isSharing.whatsapp" width="28" height="28" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
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


                        <!-- Email Share -->
                        <button @click="shareViaEmail" :disabled="isSharing.email || !currentPdfData?.email"
                            class="share-option email">
                            <div class="share-icon-wrapper email-bg">
                                <svg v-if="!isSharing.email" width="28" height="28" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
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

                        <!-- Download Again -->
                        <button @click="downloadPDF" :disabled="isSharing.downloading" class="share-option download">
                            <div class="share-icon-wrapper download-bg">
                                <svg v-if="!isSharing.downloading" width="28" height="28" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
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
                                <p v-if="isSharing.downloading">Please wait...</p>
                                <p v-else>Save copy of the PDF</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- PDF Template (Hidden) -->
        <div v-if="currentPdfData" ref="pdfTemplate" id="pdf-template" class="pdf-content relative pb-8">
            <div class="pdf-page">

                <div class="pdf-header">
                    <div class="company-info">
                        <img src="../../images/nyifeBrand.png" alt="nyife-logo" width='220' height="73"
                            style="width: 220px !important; height: 73px !important;"></img>
                        <h2>Complia Services Ltd</h2>
                        <p>nyife.chat | info@nyife.chat | +91 11 430 22 315 </p>
                        <p>Plot no.9, Third Floor, Paschim Vihar Extn. Delhi-110063, India</p>
                        <p>GSTIN: 07AALCC1963C1ZT | CIN No: U70200DL2023PLC417528</p>
                    </div>
                    <div class="invoice-info">
                        <h3>QUOTATION</h3>
                        <p><strong>Quotation #:</strong> {{ currentPdfData?.quotation_number }}</p>
                        <p><strong>Date:</strong> {{ currentPdfData?.quotation_date }}</p>
                        <p><strong>Valid Until:</strong> {{ currentPdfData?.quotation_valid_until_date }}
                        </p>
                    </div>
                </div>

                <div class="client-info">
                    <h4>Bill To:</h4>
                    <p><strong>Mr/Ms: {{ currentPdfData?.contact_person || 'N/A' }}</strong></p>
                    <p><strong>Company:</strong> {{ currentPdfData?.company_name || 'N/A' }}</p>
                    <p><strong>Phone:</strong> {{ currentPdfData?.phone || 'N/A' }}</p>
                    <p v-if="currentPdfData?.email?.trim()"><strong>Email:</strong> {{ currentPdfData?.email || 'N/A' }}
                    </p>
                    <p class="w-[70%]" v-if="currentPdfData?.address?.trim()"><strong>Address:</strong> {{
                        currentPdfData?.address ||
                        'N/A' }}</p>
                    <p v-if="currentPdfData?.gst_number?.trim()"><strong>GSTIN:</strong> {{ currentPdfData?.gst_number
                        ||
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
                        <tr v-for="(item, index) in getVisibleItems(currentPdfData, currentPdfData?.additional_fee)"
                            :key="index">
                            <td>{{ index + 1 }}</td>
                            <td>{{ item?.description }}</td>
                            <td style="text-align: center;">{{ item?.hsn_sac || 'N/A' }}</td>
                            <td style="text-align: center;">{{ item?.quantity || 1 }}</td>
                            <td style="text-align: right;">{{ formatCurrency(item?.unit_price) }}</td>
                            <td style="text-align: right;">({{ item?.discount ?? 0 }}%)
                                {{ formatCurrency(item?.discount_amount) }}</td>
                            <td style="text-align: right;">({{ item?.gst_rate ?? 18 }}%)
                                {{ formatCurrency(item?.gst_amount) }}</td>
                            <td style="text-align: right;">{{ formatCurrency(item?.amount) }}</td>
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
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import { toast } from 'vue3-toastify';
import FormPhoneInput from './FormPhoneInput.vue';
import FormInput from './FormInput.vue';
import { CirclePlus, FileText } from 'lucide-vue-next';
import axios from 'axios';
import FormDateInput from './FormDateInput.vue';

import { usePage } from '@inertiajs/vue3';
const page = usePage();

const base_url = import.meta.env.VITE_BACKEND_API_URL;
const whatsapp_token = import.meta.env.VITE_WA_TOKEN;

const Designations = ref(["Business Manager", "Sales Agent", "Customer Support Agent", "Marketing Agent", "Finance Agent"]);

const products = ref([]);
const categories = ref(["Good", "Service"]);
const isLoadingProducts = ref(false);
const selectedCategory = ref('');
const productSearchQuery = ref('');

const props = defineProps(['refresh']);
const emit = defineEmits(['update:refresh']);


const activePlans = ref([]);
const termsAndConditionsList = ref([]);
const isLoadingTerms = ref(false);

const fetchSubscriptionPlans = async () => {
    try {
        const res = await axios.get(`${base_url}/subscription-plans`);
        if (!res?.data?.success) {
            throw new Error(res?.data?.message || "Failed to fetch subscription plans");
        }
        activePlans.value = res.data.data;
    } catch (error) {
        toast.error(error.message || 'Error fetching subscription plans. Please try again.');
    }
};

// API call to fetch terms & conditions
const fetchTermsAndConditions = async () => {
    isLoadingTerms.value = true;
    try {
        const result = await axios.get(`${base_url}/terms-conditions/quotation`);

        if (result.data.success && result.data?.data?.data?.length > 0) {
            termsAndConditionsList.value = result.data.data.data;

            // Find and set primary terms & conditions as default
            const primaryTerms = result.data.data.data.find(term => term.is_primary === true);
            if (primaryTerms) {
                formData.value.termsAndConditions = primaryTerms.id;
            }
        }
    } catch (error) {
        console.error('Error fetching terms & conditions:', error);
    } finally {
        isLoadingTerms.value = false;
    }
};

const fetchProducts = async () => {
    isLoadingProducts.value = true;
    try {
        const params = new URLSearchParams({
            page: 1,
            limit: 100,
            status: 'active'
        });

        if (selectedCategory.value) {
            params.append('category', selectedCategory.value);
        }

        if (productSearchQuery.value) {
            params.append('search', productSearchQuery.value);
        }

        const response = await axios.get(`${base_url}/products?${params.toString()}`);

        if (response.data.success) {
            console.log("response.data.data : ", response.data.data.data)
            products.value = response.data.data.data;
        }
    } catch (error) {
        console.error('Error fetching products:', error);
        toast.error('Error loading products');
    } finally {
        isLoadingProducts.value = false;
    }
};


onMounted(() => {
    fetchTermsAndConditions();
    fetchSubscriptionPlans();
    fetchProducts();

    if (userType.value === 'existing') {
        fetchUsers();
    }
});


const isModalOpen = ref(false);
const isGenerating = ref(false);
const showShareOptions = ref(false);
const pdfTemplate = ref(null);
const errors = ref({});
const isSharing = ref({
    whatsapp: false,
    freeWhatsapp: false,
    email: false,
    downloading: false
});

const userType = ref('existing'); // 'existing' or 'new'
const users = ref([]);
const selectedUser = ref(null);
const isLoadingUsers = ref(false);
const searchQuery = ref('');
const pagination = ref({
    currentPage: 1,
    totalPages: 1,
    totalItems: 0,
    limit: 10
});
const searchDebounceTimer = ref(null);

watch(userType, (newVal) => {
    if (newVal === 'existing') {
        fetchUsers();
    } else {
        selectedUser.value = null;
    }
});


onMounted(() => {
    fetchTermsAndConditions();
    fetchSubscriptionPlans();
    // Add this line:
    if (userType.value === 'existing') {
        fetchUsers();
    }
});


// ==================== USER SELECTION METHODS (NEW) ====================

// Fetch all users with search and filters
const fetchUsers = async () => {
    isLoadingUsers.value = true;

    try {
        const params = new URLSearchParams({
            page: pagination.value.currentPage,
            limit: pagination.value.limit,
        });

        if (searchQuery.value) {
            params.append('search', searchQuery.value);
        }

        const response = await axios.get(`${base_url}/users?${params.toString()}`);

        const result = response.data;

        if (result.success) {
            users.value = result.data.users;
            pagination.value = {
                currentPage: result.data.pagination.currentPage,
                totalPages: result.data.pagination.totalPages,
                totalItems: result.data.pagination.totalItems,
                limit: result.data.pagination.limit
            };
        } else {
            toast.error('Failed to fetch users');
        }
    } catch (error) {
        console.error('Error fetching users:', error);
        toast.error('Error loading users');
    } finally {
        isLoadingUsers.value = false;
    }
};

// Select a user and fetch their details
const selectUser = async (user) => {
    selectedUser.value = user;

    try {
        const response = await axios.get(`${base_url}/users/${user.id}`);

        const result = response.data;

        if (result.success) {
            autofillFormData(result.data);
            toast.success(`User data loaded: ${user.first_name}`);
        } else {
            toast.error('Failed to fetch user details');
        }
    } catch (error) {
        console.error('Error fetching user details:', error);
        toast.error('Error loading user details');
    }
};

// Auto-fill form with user data
const autofillFormData = (data) => {
    const { user, invoice } = data;

    // Fill basic user info
    formData.value.contactPerson = `${user.first_name}${user.last_name ? ' ' + user.last_name : ''}`;
    formData.value.email = user.email;
    formData.value.phone = user.phone;

    // Fill invoice data if available
    if (invoice) {
        formData.value.gst_number = invoice.gst_number || '';
        formData.value.companyName = invoice.company_name || '';
        formData.value.address = invoice.address || '';

        // If GST number exists, mark it as verified
        if (invoice.gst_number) {
            gstState.value = {
                state: 'Success',
                message: 'GST loaded from previous invoice'
            };
        }
    }

    // Clear errors
    errors.value = {};
};

// Clear user selection
const clearUserSelection = () => {
    selectedUser.value = null;
    // Don't reset entire form, just clear auto-filled fields if needed
};

// Debounced search
const debouncedSearch = () => {
    clearTimeout(searchDebounceTimer.value);
    searchDebounceTimer.value = setTimeout(() => {
        pagination.value.currentPage = 1;
        fetchUsers();
    }, 500);
};

// Clear search
const clearSearch = () => {
    searchQuery.value = '';
    pagination.value.currentPage = 1;
    fetchUsers();
};

// Pagination
const goToPage = (page) => {
    if (page >= 1 && page <= pagination.value.totalPages) {
        pagination.value.currentPage = page;
        fetchUsers();
    }
};

// Get initials for avatar
const getInitials = (firstName, lastName) => {
    const first = firstName ? firstName.charAt(0).toUpperCase() : '';
    const last = lastName ? lastName.charAt(0).toUpperCase() : '';
    return first + last || '??';
};

// ==================== END USER SELECTION METHODS ====================

// Helper function to calculate default dates
const getDefaultDateByMonths = (months) => {
    const date = new Date();
    date.setMonth(date.getMonth() + months);

    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
};


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
    serviceChargeHsnSac: '998314',  // NEW
    serviceChargeGstRate: 18, // Default 18%
    walletRecharge: 0,
    setupFee: 0,
    customizationFee: 0,
    discount: 0,
    quotation_valid_until_date: '',
    signature: page?.props?.auth?.user?.full_name || page?.props?.auth?.user?.first_name || '',
    designation: Designations.value[0],
    termsAndConditions: '',
});

const additionalItems = ref([]);

// Helper to create new item with product structure
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

const currentPdfData = ref(null);

const gstState = ref({
    state: "Initial",
    message: ""
});

const verifyGst = async () => {

    gstState.value.state = "Loading";
    gstState.value.message = `Verifying... ( It may take up to 1 minute, kindly wait )`;

    try {
        const res = await axios.post(
            `${base_url}/gst/verify`,
            { gst_number: formData.value.gst_number }
        );

        if (res.data.success) {
            formData.value.address = res.data.data.gst_data.Principal_Place_of_Business;
            formData.value.companyName = res.data.data.gst_data.LegalName_of_Busines;

            gstState.value.state = "Success";
            gstState.value.message = res.data.message || `GST Verified`;
            return;
        }

        throw new Error(res.data.message || "Verification failed");


    } catch (err) {
        gstState.value.state = "Error";
        gstState.value.message = err.response.data.message || err.message;
    } finally {
        setTimeout(() => {
            gstState.value.state = "Initial";
            gstState.value.message = "";
        }, 5000);
    }
};

const openModal = () => {
    isModalOpen.value = true;
    errors.value = {};
};

const closeModal = () => {
    isModalOpen.value = false;
    resetForm();
};

const closeShareModal = () => {
    showShareOptions.value = false;
    resetForm();
};

const resetForm = () => {
    formData.value = {
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
        signature: page?.props?.auth?.user?.full_name || page?.props?.auth?.user?.first_name || '',
        designation: Designations.value[0],
        termsAndConditions: formData.value.termsAndConditions,
    };
    additionalItems.value = [];
    errors.value = {};
    selectedUser.value = null;
    userType.value = 'existing';
    searchQuery.value = '';
    selectedCategory.value = '';
    productSearchQuery.value = '';
};

const validateForm = () => {
    errors.value = {};
    let isValid = true;

    if (!formData.value.companyName.trim()) {
        errors.value.companyName = 'Company name is required';
        isValid = false;
    }

    if (formData.value.gst_number?.trim()?.length !== 15) {
        if (!formData.value.gst_number.trim()) {
            return true;
        } else {
            errors.value.gst_number = 'Enter valid GST number';
            isValid = false;
        }
    }

    if (!formData.value.termsAndConditions) {
        errors.value.termsAndConditions = 'Terms & Conditions is required';
        isValid = false;
    }

    if (!formData.value.contactPerson.trim()) {
        errors.value.contactPerson = 'Contact person name is required';
        isValid = false;
    }

    if (!formData.value.phone.trim()) {
        errors.value.phone = 'Phone number is required';
        isValid = false;
    }


    if (!formData.value.email.trim()) {
        errors.value.email = 'Email is required';
        isValid = false;
    }

    if (!formData.value.signature.trim()) {
        errors.value.signature = 'Signature is required';
        isValid = false;
    }

    if (!formData.value.designation.trim()) {
        errors.value.designation = 'Designation is required';
        isValid = false;
    }

    if (!formData.value.quotation_valid_until_date) {
        errors.value.quotation_valid_until_date = 'Quotation Validity Date is required';
        isValid = false;
    }

    if (calculateSubtotal() === 0) {
        toast.error('Please add at least one item with an amount greater than 0');
        isValid = false;
    }

    return isValid;
};

const handleQuotationDateChange = (newDate) => {
    // Your validation logic here
    if (!newDate) {
        errors.value.quotation_valid_until_date = 'Please select a date';
    } else {
        errors.value.quotation_valid_until_date = '';
    }
};

const addAdditionalItem = () => {
    additionalItems.value.push(createNewItem());
};

const handleProductSelect = (index, productId) => {
    const product = products.value.find(p => p.id === productId);

    // data
    const unit_price = parseFloat(product.amount) ?? 0;
    const gst_rate = parseFloat(product.gst_rate) ?? 18;
    const base_amount = unit_price;
    const gst_amount = base_amount * (gst_rate / 100);
    const total_amount = base_amount + gst_amount;

    if (product) {
        additionalItems.value[index] = {
            productId: product.id,
            description: product.name,
            quantity: 1,
            unit_price: unit_price,
            discount: 0,
            hsn_sac: product.hsn_sac || '',
            gst_rate: gst_rate,
            base_amount: base_amount,
            discount_amount: 0,
            gst_amount: gst_amount,
            amount: total_amount
        };
    }
};

const handleQuantityChange = (index) => {
    const item = additionalItems.value[index];

    // Calculate base amount (quantity × unit price)
    const baseAmount = item.quantity * item.unit_price;

    // Apply item-level discount
    const discountPercent = parseFloat(item.discount) ?? 0;
    const discountAmount = baseAmount * (discountPercent / 100);
    const amountAfterDiscount = baseAmount - discountAmount;

    // Apply GST on discounted amount
    const gstRate = parseFloat(item.gst_rate) ?? 18;
    const gstAmount = amountAfterDiscount * (gstRate / 100);

    // Final amount = Amount after discount + GST
    item.base_amount = parseFloat((baseAmount).toFixed(2));
    item.discount_amount = parseFloat((discountAmount).toFixed(2));
    item.gst_amount = parseFloat((gstAmount).toFixed(2));
    item.amount = parseFloat((amountAfterDiscount + gstAmount).toFixed(2));
};

const updatePlatformChargeDetails = () => {
    const selectedPlan = activePlans.value.find(plan => plan.id === formData.value.selectedPlanId);
    if (selectedPlan) {
        formData.value.platformCharge = parseFloat(selectedPlan.price);
        formData.value.platformChargeType = selectedPlan.name;
        // Set HSN/SAC for platform services (usually 998314 for IT services)
        formData.value.serviceChargeHsnSac = '998314';
        formData.value.serviceChargeGstRate = 18;
    }
};

const removeAdditionalItem = (index) => {
    additionalItems.value.splice(index, 1);
};

const formatCurrency = (value) => {
    const num = parseFloat(value) || 0;
    return num.toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
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

    let total = 0;
    if (formData.value.platformCharge) total += parseFloat(formData.value.platformCharge) || 0;
    if (formData.value.walletRecharge) total += parseFloat(formData.value.walletRecharge) || 0;
    if (formData.value.setupFee) total += parseFloat(formData.value.setupFee) || 0;
    if (formData.value.customizationFee) total += parseFloat(formData.value.customizationFee) || 0;

    const additionalItemsDiscount = additionalItems.value.reduce((acc, item) => {
        return acc + Number(item.discount_amount ?? 0);
    }, 0);

    return ((total * (discount / 100)) + (additionalItemsDiscount || 0));
};

const calculateAmountAfterDiscount = () => {
    return calculateSubtotal() - calculateDiscount();
};

const calculateGST = () => {
    const toNumber = (v) => Number(v) || 0;

    // 1️⃣ Sum all service amounts
    const totalServiceAmount =
        toNumber(formData.value.platformCharge) +
        toNumber(formData.value.walletRecharge) +
        toNumber(formData.value.setupFee) +
        toNumber(formData.value.customizationFee);

    // 2️⃣ Apply discount
    const discountRate = toNumber(formData.value.discount);
    const discountedServiceAmount =
        totalServiceAmount * (1 - discountRate / 100);

    // 3️⃣ Service GST
    const serviceGstRate = toNumber(formData.value.serviceChargeGstRate);
    const serviceGst =
        discountedServiceAmount * (serviceGstRate / 100);

    // 4️⃣ Additional items GST
    const additionalGst = additionalItems.value.reduce(
        (sum, item) => sum + toNumber(item.gst_amount),
        0
    );

    // 5️⃣ Total GST (rounded once)
    const totalGst = serviceGst + additionalGst;

    return Number(totalGst.toFixed(2));
};


const calculateTotal = () => {
    return calculateAmountAfterDiscount() + calculateGST();
};

const totalRows = computed(() => {
    let count = 0;

    if (formData.value?.platformCharge > 0) count++;
    if (formData.value?.walletRecharge > 0) count++;
    if (formData.value?.setupFee > 0) count++;
    if (formData.value?.customizationFee > 0) count++;

    if (additionalItems.value?.length) {
        count += additionalItems.value.length;
    }

    return count;
});


const getVisibleItems = (Data, additionalData) => {
    const items = [];

    if (parseFloat(Data?.platform_charge) > 0) {

        // input values
        const unit_price = Number(Data?.platform_charge ?? 0);
        const discount = Number(Data?.discount ?? 0); // %
        const gst_rate = Number(Data?.GST ?? 0);       // %

        // calculations
        const discount_amount = Math.round(unit_price * discount) / 100;

        const discounted_price = unit_price - discount_amount;

        const gst_amount = Math.round(discounted_price * gst_rate) / 100;

        const total_amount = Math.round((discounted_price + gst_amount) * 100) / 100;


        items.push({
            description: `Platform Charge (${Data?.platform_charge_type})`,
            quantity: 1,
            unit_price: unit_price,
            discount: discount,
            amount: total_amount,
            hsn_sac: Data?.service_charge_hsn_sac || '998314',
            gst_rate: gst_rate,
            discount_amount: discount_amount,
            gst_amount: gst_amount,
        });
    }

    if (parseFloat(Data?.wallet_recharge) > 0) {

        // input values
        const unit_price = Number(Data?.wallet_recharge ?? 0);
        const discount = Number(Data?.discount ?? 0); // %
        const gst_rate = Number(Data?.GST ?? 0);       // %

        // calculations
        const discount_amount = Math.round(unit_price * discount) / 100;

        const discounted_price = unit_price - discount_amount;

        const gst_amount = Math.round(discounted_price * gst_rate) / 100;

        const total_amount = Math.round((discounted_price + gst_amount) * 100) / 100;

        items.push({
            description: 'Wallet Recharge',
            quantity: 1,
            unit_price: unit_price,
            discount: discount,
            amount: total_amount,
            hsn_sac: Data?.service_charge_hsn_sac || '998314',
            gst_rate: gst_rate,
            discount_amount: discount_amount,
            gst_amount: gst_amount,
        });
    }

    if (parseFloat(Data?.setup_fee) > 0) {

        // input values
        const unit_price = Number(Data?.setup_fee ?? 0);
        const discount = Number(Data?.discount ?? 0); // %
        const gst_rate = Number(Data?.GST ?? 0);       // %

        // calculations
        const discount_amount = Math.round(unit_price * discount) / 100;

        const discounted_price = unit_price - discount_amount;

        const gst_amount = Math.round(discounted_price * gst_rate) / 100;

        const total_amount = Math.round((discounted_price + gst_amount) * 100) / 100;

        items.push({
            description: 'Setup Fee',
            quantity: 1,
            unit_price: unit_price,
            discount: discount,
            amount: total_amount,
            hsn_sac: Data?.service_charge_hsn_sac || '998314',
            gst_rate: gst_rate,
            discount_amount: discount_amount,
            gst_amount: gst_amount,
        });
    }

    if (parseFloat(Data?.customization_fee) > 0) {

        // input values
        const unit_price = Number(Data?.customization_fee ?? 0);
        const discount = Number(Data?.discount ?? 0); // %
        const gst_rate = Number(Data?.GST ?? 0);       // %

        // calculations
        const discount_amount = Math.round(unit_price * discount) / 100;

        const discounted_price = unit_price - discount_amount;

        const gst_amount = Math.round(discounted_price * gst_rate) / 100;

        const total_amount = Math.round((discounted_price + gst_amount) * 100) / 100;

        items.push({
            description: 'Customization Fee',
            quantity: 1,
            unit_price: unit_price,
            discount: discount,
            amount: total_amount,
            hsn_sac: Data?.service_charge_hsn_sac || '998314',
            gst_rate: gst_rate,
            discount_amount: discount_amount,
            gst_amount: gst_amount,
        });
    }

    additionalData?.forEach(item => {
        if (item?.description && parseFloat(item?.amount) > 0) {
            items.push({
                description: item?.description,
                quantity: item?.quantity || 1,
                unit_price: parseFloat(item?.unit_price) || 0,
                discount: parseFloat(item?.discount) || 0,
                hsn_sac: item?.hsn_sac || '',
                gst_rate: item?.gst_rate || 0,
                base_amount: parseFloat(item?.base_amount),
                discount_amount: parseFloat(item?.discount_amount),
                gst_amount: parseFloat(item?.gst_amount),
                amount: parseFloat(item?.amount),
            });
        }
    });

    return items;
};

// Get the full terms object for the selected ID
const getSelectedTerms = () => {
    return termsAndConditionsList.value.find(
        term => term.id === formData.value.termsAndConditions
    );
};

// Use this when generating the PDF to include the actual terms
const selectedTermsData = computed(() => getSelectedTerms());

const generatePDFBlob = async () => {
    // Constants for PDF generation
    const PDF_CONFIG = {
        A4_WIDTH_MM: 210,
        A4_HEIGHT_MM: 297,
        A4_WIDTH_PX: 793.7, // 210mm at 96 DPI
        PADDING_MM: 10,
        CANVAS_SCALE: 4, // Reduced from 5 for better performance, still high quality
        IMAGE_QUALITY: 0.95, // Slightly reduced for smaller file size
        RENDER_DELAY_MS: 200
    };

    try {
        // Wait for any pending DOM updates
        await new Promise(resolve => setTimeout(resolve, PDF_CONFIG.RENDER_DELAY_MS));

        const element = pdfTemplate.value;
        if (!element) {
            throw new Error('PDF template element not found');
        }

        // Get the two content sections with validation
        const firstPageContent = element.querySelector('.pdf-content > div:first-child');
        const secondPageContent = element.querySelector('.pdf-content > div:last-child');

        if (!firstPageContent || !secondPageContent) {
            throw new Error('Required content sections not found in PDF template');
        }

        // Validate Quotation data
        if (!currentPdfData?.value) {
            throw new Error('Quotation data is not available');
        }

        // Create optimized page element factory
        const createPageElement = (content) => {
            const pageDiv = document.createElement('div');

            // Apply styles in batch for performance
            Object.assign(pageDiv.style, {
                width: `${PDF_CONFIG.A4_WIDTH_MM}mm`,
                minHeight: `${PDF_CONFIG.A4_HEIGHT_MM}mm`,
                padding: `${PDF_CONFIG.PADDING_MM}mm`,
                backgroundColor: '#ffffff',
                boxSizing: 'border-box',
                position: 'relative',
                fontSmoothing: 'antialiased',
                WebkitFontSmoothing: 'antialiased'
            });

            // Deep clone to avoid reference issues
            pageDiv.appendChild(content.cloneNode(true));
            return pageDiv;
        };

        // Create temporary container with optimized positioning
        const tempContainer = document.createElement('div');
        Object.assign(tempContainer.style, {
            position: 'fixed',
            left: '0',
            top: '0',
            zIndex: '-1000',
            opacity: '0',
            pointerEvents: 'none'
        });
        document.body.appendChild(tempContainer);

        // Shared html2canvas configuration
        const canvasConfig = {
            scale: PDF_CONFIG.CANVAS_SCALE,
            useCORS: true,
            allowTaint: false,
            logging: false,
            backgroundColor: '#ffffff',
            width: PDF_CONFIG.A4_WIDTH_PX,
            windowWidth: PDF_CONFIG.A4_WIDTH_PX,
            imageTimeout: 15000,
            removeContainer: false
        };

        // Render first page
        const firstPage = createPageElement(firstPageContent);
        tempContainer.appendChild(firstPage);

        const canvas1 = await html2canvas(firstPage, canvasConfig);

        // Render second page (reuse container for memory efficiency)
        tempContainer.innerHTML = '';
        const secondPage = createPageElement(secondPageContent);
        tempContainer.appendChild(secondPage);

        const canvas2 = await html2canvas(secondPage, canvasConfig);

        // Clean up DOM
        document.body.removeChild(tempContainer);

        // Initialize PDF with compression
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4',
            compress: true,
            hotfixes: ['px_scaling']
        });

        // Helper function to add image to PDF with proper scaling
        const addImageToPDF = (canvas, pageIndex = 0) => {
            const imgData = canvas.toDataURL('image/jpeg', PDF_CONFIG.IMAGE_QUALITY);
            const imgWidth = PDF_CONFIG.A4_WIDTH_MM;
            const imgHeight = (canvas.height * PDF_CONFIG.A4_WIDTH_MM) / canvas.width;

            // Ensure image doesn't exceed page height
            const finalHeight = Math.min(imgHeight, PDF_CONFIG.A4_HEIGHT_MM);

            if (pageIndex > 0) {
                pdf.addPage();
            }

            pdf.addImage(imgData, 'JPEG', 0, 0, imgWidth, finalHeight, undefined, 'FAST');
        };

        // Add pages to PDF
        addImageToPDF(canvas1, 0);
        addImageToPDF(canvas2, 1);

        // Generate sanitized filename
        const sanitizeFilename = (str) => {
            return str ? str.replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 100) : 'Unknown';
        };

        const quotationNumber = sanitizeFilename(currentPdfData?.value?.quotation_number || 'N/A');
        const companyName = sanitizeFilename(currentPdfData?.value?.company_name || 'Company');

        const fileName = `Quotation_${quotationNumber}_${companyName}.pdf`;

        // Generate output
        const pdfBlob = pdf.output('blob');

        // Save PDF with the sanitized filename
        // pdf.save(fileName);

        // Return blob and metadata
        return {
            blob: pdfBlob,
            pdf,
            fileName,
            size: pdfBlob.size
        };

    } catch (error) {
        throw new Error(`Failed to generate PDF: ${error.message}`);
    }
};

const generatePDF = async () => {
    if (!validateForm()) {
        return;
    }

    isGenerating.value = true;

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
                unit_price: item.unit_price,// 100
                quantity: item.quantity,// 2
                base_amount: item.base_amount,// 200
                discount: item.discount,// 10
                discount_amount: item.discount_amount, // 20
                gst_rate: item.gst_rate,// 18
                gst_amount: item.gst_amount, // 32.4
                amount: item.amount, // 212.4
            })),
            discount: formData.value.discount,
            quotation_valid_until_date: formData.value.quotation_valid_until_date,
            signature: formData.value.signature,
            designation: formData.value.designation,
            created_by: page?.props?.auth?.user?.id || null,
        };

        const res = await axios.post(`${base_url}/invoices`, payload);

        if (!res?.data?.success) {
            throw new Error(res?.data?.message || "Failed to create quotation");
        }

        currentPdfData.value = res?.data?.data;

        try {
            const { blob, fileName } = await generatePDFBlob();

            const formData = new FormData();
            formData.append("pdf_data", blob, fileName);
            formData.append("pdf_type", "quotation");
            formData.append("id", res?.data?.data.id);

            const uploadRes = await axios.post(`${base_url}/uploads`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                }
            });

            if (!uploadRes?.data?.success) {
                throw new Error(uploadRes?.data?.message || "Failed to upload quotation PDF");
            }

            toast.success('Quotation generated successfully!');
            currentPdfData.value.quotation_invoice_pdf_url = uploadRes.data.data.file.url;
            currentPdfData.value.quotation_invoice_pdf_download_url = uploadRes.data.data.file.downloadUrl;
            currentPdfData.value.quotation_invoice_pdf_filename = uploadRes.data.data.file.filename;
            // Close the form modal
            isModalOpen.value = false;

            // Show share options modal after a short delay
            setTimeout(() => {
                showShareOptions.value = true;
            }, 300);

        } catch (error) {
            if (currentPdfData?.value?.id) {
                await axios.delete(`${base_url}/invoices/${currentPdfData?.value?.id}`);
            }
            toast.error(error.message || 'Error generating PDF. Please try again.');
        }

    } catch (error) {
        toast.error(error.message || 'Error generating quotation. Please try again.');
    } finally {
        isGenerating.value = false;
        emit('update:refresh', !props.refresh);
    }
};

// const generatePDF = async () => {
//     if (!validateForm()) {
//         return;
//     }

//     isGenerating.value = true;

//     try {

//         const payload = {
//             company_name: formData.value.companyName,
//             contact_person: formData.value.contactPerson,
//             gst_number: formData.value.gst_number || null,
//             phone: formData.value.phone,
//             email: formData.value.email,
//             address: formData.value.address,
//             selected_plan_id: formData.value.selectedPlanId,
//             platform_charge_type: formData.value.platformChargeType,
//             platform_charge: formData.value.platformCharge,
//             wallet_recharge: formData.value.walletRecharge,
//             setup_fee: formData.value.setupFee,
//             customization_fee: formData.value.customizationFee,
//             additional_fee: additionalItems.value,
//             discount: formData.value.discount,
//             GST: 18,
//             quotation_valid_until_date: formData.value.quotation_valid_until_date,
//             signature: formData.value.signature,
//             designation: formData.value.designation,
//             created_by: page?.props?.auth?.user?.id || null,
//         }

//         const res = await axios.post(`${base_url}/invoices`, payload);


//         if (!res?.data?.success) {
//             throw new Error(res?.data?.message || "Failed to create quotation");
//         }

//         currentPdfData.value = res?.data?.data;

//         try {
//             const { blob, fileName } = await generatePDFBlob();

//             const formData = new FormData();
//             formData.append("pdf_data", blob, fileName);
//             formData.append("pdf_type", "quotation");
//             formData.append("id", res?.data?.data.id);

//             const uploadRes = await axios.post(`${base_url}/uploads`, formData, {
//                 headers: {
//                     'Content-Type': 'multipart/form-data',
//                 }
//             });

//             if (!uploadRes?.data?.success) {
//                 throw new Error(uploadRes?.data?.message || "Failed to upload quotation PDF");
//             }

//             toast.success('Quotation generated successfully!');
//             currentPdfData.value.quotation_invoice_pdf_url = uploadRes.data.data.file.url;
//             currentPdfData.value.quotation_invoice_pdf_download_url = uploadRes.data.data.file.downloadUrl;
//             currentPdfData.value.quotation_invoice_pdf_filename = uploadRes.data.data.file.filename;
//             // Close the form modal
//             isModalOpen.value = false;

//             // Show share options modal after a short delay
//             setTimeout(() => {
//                 showShareOptions.value = true;
//             }, 300);

//         } catch (error) {
//             if (currentPdfData?.value?.id) {
//                 await axios.delete(`${base_url}/invoices/${currentPdfData?.value?.id}`);
//             }
//             toast.error(error.message || 'Error generating PDF. Please try again.');
//         }


//     } catch (error) {
//         toast.error(error.message || 'Error generating quotation. Please try again.');
//     } finally {
//         isGenerating.value = false;
//         emit('update:refresh', !props.refresh);
//     }
// };

const downloadPDF = async () => {
    if (!currentPdfData?.value?.quotation_invoice_pdf_download_url) {
        toast.error('No PDF available to download');
        return;
    }

    isSharing.value.downloading = true;

    try {
        // Fetch the PDF as a blob
        const response = await axios.get(
            currentPdfData?.value?.quotation_invoice_pdf_download_url,
            {
                responseType: "blob", // important
            }
        );

        const blob = response.data;


        // Create a blob URL
        const blobUrl = window.URL.createObjectURL(blob);

        // Create and trigger download
        const link = document.createElement('a');
        link.href = blobUrl;
        link.download = currentPdfData?.value?.quotation_invoice_pdf_filename || "quotation.pdf";
        document.body.appendChild(link);
        link.click();

        // Cleanup
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

    // ========================== SECOND METHOD ========================


    //     const message = `
    // Hi Sir,

    // Thank you for your interest! Please download your ${currentPdfData.value.templateType?.toLowerCase()} invoice by clicking on the link below:

    // If you have any questions or need any changes, feel free to reply here.

    // Looking forward to assisting you.

    // Url : ${currentPdfData.value.pdfDownloadUrl}
    //     `.trim();

    //     const encodedMessage = encodeURIComponent(message);

    //     // remove spaces & non-digits, keep country code
    //     const phone = currentPdfData.value.phone.replace(/\D/g, '');

    //     const whatsappUrl = `https://wa.me/${phone}?text=${encodedMessage}`;

    //     window.open(whatsappUrl, '_blank');

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
        }

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
/* Modal Overlay */
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

/* Modal Container */
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

/* Modal Header */
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

/* Modal Body */
.modal-body {
    padding: 1.5rem;
    overflow-y: auto;
}

/* User Type Selector */
.user-type-selector {
    margin-bottom: 1.5rem;
}

.radio-group {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-top: 0.75rem;
}

.radio-option {
    position: relative;
    cursor: pointer;
}

.radio-option input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.radio-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 0.5rem;
    background: white;
    transition: all 0.2s;
}

.radio-option:hover .radio-content {
    border-color: #cbd5e1;
    background: #f8fafc;
}

.radio-option.active .radio-content {
    border-color: #ff5100;
    background: #fff5f0;
}

.radio-content svg {
    color: #64748b;
}

.radio-option.active .radio-content svg {
    color: #ff5100;
}

.radio-content span {
    font-weight: 500;
    color: #334155;
}

/* User Selection Section */
.user-selection-section {
    background: #f8fafc;
    border-radius: 0.75rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

/* Search and Filter */
.search-filter-container {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 1rem;
    margin-bottom: 1rem;
}

.search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.search-box svg {
    position: absolute;
    left: 1rem;
    color: #94a3b8;
}

.search-box input {
    width: 100%;
    padding: 0.75rem 2.5rem 0.75rem 2.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.search-box input:focus {
    outline: none;
    border-color: #ff5100;
    box-shadow: 0 0 0 3px rgba(255, 81, 0, 0.1);
}

.clear-btn {
    position: absolute;
    right: 0.75rem;
    padding: 0.25rem;
    border: none;
    background: transparent;
    color: #94a3b8;
    cursor: pointer;
    border-radius: 0.25rem;
    transition: all 0.2s;
}

.clear-btn:hover {
    background: #f1f5f9;
    color: #64748b;
}

/* Selected User Card */
.selected-user-card {
    background: white;
    border: 2px solid #ff5100;
    border-radius: 0.75rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.selected-user-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.selected-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #ff5100;
}

.change-btn {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    background: white;
    color: #64748b;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s;
}

.change-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}

.selected-user-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-avatar {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff5100 0%, #ff7d47 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.125rem;
}

.user-details {
    flex: 1;
}

.user-details .user-name {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.user-details .user-meta {
    font-size: 0.875rem;
    color: #64748b;
}

/* Users List Container */
.users-list-container {
    background: white;
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    gap: 1rem;
    color: #64748b;
}

.spinner {
    width: 2.5rem;
    height: 2.5rem;
    border: 3px solid #e2e8f0;
    border-top-color: #ff5100;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    color: #94a3b8;
}

.empty-state svg {
    margin-bottom: 1rem;
}

.empty-state p {
    font-weight: 500;
    color: #64748b;
    margin-bottom: 0.25rem;
}

.empty-state span {
    font-size: 0.875rem;
}

/* Users List */
.users-list {
    max-height: 400px;
    overflow-y: auto;
}

.user-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid #e2e8f0;
    cursor: pointer;
    transition: all 0.2s;
}

.user-card:last-child {
    border-bottom: none;
}

.user-card:hover {
    background: #f8fafc;
}

.user-avatar-small {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff5100 0%, #ff7d47 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.user-info {
    flex: 1;
    min-width: 0;
}

.user-name-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
}

.user-info .user-name {
    font-weight: 500;
    color: #1e293b;
}

.user-role-badge {
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.role-admin {
    background: #fef3c7;
    color: #92400e;
}

.role-staff {
    background: #dbeafe;
    color: #1e40af;
}

.role-user {
    background: #e0e7ff;
    color: #3730a3;
}

.user-contact {
    font-size: 0.875rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    overflow: hidden;
}

.user-contact span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.separator {
    flex-shrink: 0;
}

.chevron-icon {
    color: #cbd5e1;
    flex-shrink: 0;
}

/* Pagination */
.pagination-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    border-top: 1px solid #e2e8f0;
    background: #fafbfc;
}

.pagination-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    background: white;
    color: #475569;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s;
}

.pagination-btn:hover:not(:disabled) {
    background: #f8fafc;
    border-color: #cbd5e1;
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-info {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
}

.total-items {
    color: #94a3b8;
    font-weight: 400;
}

/* Form Section */
.form-section {
    margin-top: 1.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .radio-group {
        grid-template-columns: 1fr;
    }

    .search-filter-container {
        grid-template-columns: 1fr;
    }

    .pagination-container {
        flex-direction: column;
        gap: 1rem;
    }

    .pagination-btn {
        width: 100%;
        justify-content: center;
    }

    .user-contact {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }

    .separator {
        display: none;
    }
}

.subtitle {
    color: #6b7280;
    margin-bottom: 1.5rem;
}

/* Section Title */
.section-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin: 1.5rem 0 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e5e7eb;
}

.section-title.no-border {
    border-bottom: none;
    margin-bottom: 0.5rem;
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

.required {
    color: #ef4444;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 12px 14px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    font-family: inherit;
    background: white;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #ff5100;
    box-shadow: 0 0 0 3px rgba(255, 81, 0, 0.1);
}

.form-group input:hover,
.form-group select:hover,
.form-group textarea:hover {
    border-color: #cbd5e0;
}

.error-input {
    border-color: #e53e3e !important;
}

.error-text {
    color: #e53e3e;
    font-size: 12px;
    margin-top: 4px;
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

.item-row {
    display: grid;
    grid-template-columns: 2fr 1fr auto;
    gap: 15px;
    margin-bottom: 15px;
    align-items: center;
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

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #48bb78;
    color: white;
}

.btn-secondary:hover {
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

/* PDF Template Styles */
#pdf-template {
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

/* Share Modal Styles */
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

.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.btn-outline {
    background: white;
    color: #374151;
    border: 2px solid #e5e7eb;
}

.btn-outline:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

.w-full {
    width: 100%;
}

.mt-6 {
    margin-top: 1.5rem;
}

.product-filters {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
    border: 1px solid #e2e8f0;
}

.item-row-extended {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.item-row-extended:hover {
    border-color: #cbd5e1;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.item-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
    margin-bottom: 1rem;
}

.item-row-extended select {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}

.item-row-extended select:hover {
    border-color: #cbd5e1;
}

.item-row-extended select:focus {
    outline: none;
    border-color: #ff5100;
    box-shadow: 0 0 0 3px rgba(255, 81, 0, 0.1);
}

.item-row-extended select option {
    padding: 0.5rem;
}

.item-row-extended .btn-danger {
    margin-top: 0;
    width: 100%;
    justify-content: center;
}

input[readonly] {
    background-color: #f9fafb !important;
    cursor: not-allowed !important;
    color: #6b7280;
}


.item-row-extended select:disabled {
    background-color: #f3f4f6;
    cursor: not-allowed;
    opacity: 0.6;
}

.product-info-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background: #eff6ff;
    color: #1e40af;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    margin-left: 0.5rem;
}

.summary-box .gst-breakdown {
    font-size: 0.875rem;
    color: #6b7280;
    padding: 0.5rem 0;
    border-top: 1px dashed #e5e7eb;
    margin-top: 0.5rem;
}

.summary-box .gst-breakdown span {
    display: block;
    margin: 0.25rem 0;
}

.item-type-badge {
    display: inline-block;
    padding: 0.125rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
    margin-left: 0.5rem;
}

.item-type-badge.product {
    background: #dbeafe;
    color: #1e40af;
}

.item-type-badge.custom {
    background: #fef3c7;
    color: #92400e;
}
</style>