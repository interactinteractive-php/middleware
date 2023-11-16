<?php echo $this->layout; ?>
<style>
    .hidden-left-links {
        display: none;
    }
</style>
<script type="text/javascript">
var posStoreId = '<?php echo Session::get(SESSION_PREFIX.'storeId'); ?>', 
    isConfigDelivery = <?php echo $this->isConfigDelivery; ?>,        
    isConfigSalesPerson = <?php echo $this->isConfigSalesPerson ?>, 
    isConfigServiceJob = <?php echo $this->isConfigServiceJob; ?>,      
    isConfigHealthRecipe = <?php echo $this->isConfigHealthRecipe; ?>,
    isConfigSerialNumber = <?php echo $this->isConfigSerialNumber; ?>,
    isConfigItemCheckDuplicate = <?php echo $this->isConfigItemCheckDuplicate; ?>, 
    isConfigItemCheckDiscountQty = <?php echo $this->isConfigItemCheckDiscountQty; ?>, 
    isConfigItemCheckEndQty = <?php echo $this->isConfigItemCheckEndQty; ?>, 
    isConfigItemCheckEndQtyMsg = <?php echo $this->isConfigItemCheckEndQtyMsg; ?>, 
    isConfigRowDiscount = <?php echo $this->isConfigRowDiscount; ?>, 
    isConfigDescriptionRequired = <?php echo $this->isConfigDescriptionRequired; ?>, 
    isConfigOnlyInvDescrRequired = <?php echo $this->isConfigOnlyInvDescrRequired; ?>, 
    isConfigEmpCustomer = <?php echo $this->isConfigEmpCustomer; ?>, 
    isConfigUseCandy = <?php echo $this->isConfigUseCandy; ?>, 
    isNotSendVatsp = <?php echo $this->isNotSendVatsp; ?>, 
    isBasketOnly = <?php echo $this->isBasketOnly; ?>, 
    isConfigInvoiceList = <?php echo $this->isConfigInvoiceList; ?>, 
    
    isConfigPaymentCoupon = <?php echo $this->isConfigPaymentCoupon; ?>,
    isConfigPrePayment = <?php echo $this->isConfigPrePayment; ?>,
    isConfigPaymentBonuscard = <?php echo $this->isConfigPaymentBonuscard; ?>,
    isConfigPaymentDiscountActivity = <?php echo $this->isConfigPaymentDiscountActivity; ?>,
    isConfigPaymentInsurance = <?php echo $this->isConfigPaymentInsurance; ?>,
    isConfigPaymentAccountTransfer = <?php echo $this->isConfigPaymentAccountTransfer; ?>,
    isConfigPaymentMobilenet = <?php echo $this->isConfigPaymentMobilenet; ?>,
    isConfigPaymentOther = <?php echo $this->isConfigPaymentOther; ?>,
    isConfigPaymentTcard = <?php echo $this->isConfigPaymentTcard; ?>,
    isConfigPaymentShoppy = <?php echo $this->isConfigPaymentShoppy; ?>,
    isConfigPaymentGlmtreward = <?php echo $this->isConfigPaymentGlmtreward; ?>,
    isConfigPaymentSocialpayreward = <?php echo $this->isConfigPaymentSocialpayreward; ?>,
    isConfigPaymentBarter = <?php echo $this->isConfigPaymentBarter; ?>,
    isConfigPaymentLeasing = <?php echo $this->isConfigPaymentLeasing; ?>,
    isConfigPaymentEmpLoan = <?php echo $this->isConfigPaymentEmpLoan; ?>,
    isConfigPaymentLocalExpense = <?php echo $this->isConfigPaymentLocalExpense; ?>,
    isConfigPaymentUnitReceivable = <?php echo $this->isConfigPaymentUnitReceivable; ?>,
    isConfigPaymentReceivable = <?php echo $this->isConfigPaymentReceivable; ?>,
    isConfigPaymentCandy = <?php echo $this->isConfigPaymentCandy; ?>,
    isConfigPaymentUpoint = <?php echo $this->isConfigPaymentUpoint; ?>,
    isConfigPaymentCandyCoupon = <?php echo $this->isConfigPaymentCandyCoupon; ?>,
    isConfigPaymentDelivery = <?php echo $this->isConfigPaymentDelivery; ?>,
    isConfigPaymentLendMn = <?php echo $this->isConfigPaymentLendMn; ?>,
    isConfigClearSidebarData = <?php echo $this->isConfigClearSidebarData; ?>,
    
    tempInvoiceDvId = '<?php echo $this->tempInvoiceDvId; ?>',
    bankIpterminals = <?php echo Session::get(SESSION_PREFIX.'ipterminals') ? json_encode(Session::get(SESSION_PREFIX.'ipterminals')) : '{}'; ?>,
    
    isPOSLayoutAjaxLoad = <?php echo json_encode($this->isAjaxLoad); ?>, 
    isTalonListProtect = <?php echo $this->isTalonListProtect; ?>, 
    amountAPad = false, 
    isReceiptNumber = false,
    isDisableRowDiscountInput = false,
    isReturnCustomerInfoRequired = <?php echo $this->isReturnCustomerInfoRequired; ?>, 
    isBeforePrintAskLoyaltyPoint = true, 
    isItemSearchEmptyFocus = false, 
    isTodayReturn = false, 
    receiptRegNumber = '',
    tbltCount = 0, 
    drugPrescription = [], 
    returnBillType = '', 
    candyQrUuid = '',
    posUseIpTerminal = '<?php echo Session::get(SESSION_PREFIX.'posUseIpTerminal'); ?>',
    isConfirmSaleDate = '<?php echo Session::get(SESSION_PREFIX.'isConfirmSaleDate'); ?>',
    isPosActiveLogin = '<?php echo Session::get(SESSION_PREFIX.'posActiveLogin'); ?>',
    dataViewId = '<?php echo issetParam($this->dataViewId); ?>',
    cashierId = '<?php echo Session::get(SESSION_PREFIX.'cashierId'); ?>',
    cashRegisterId = '<?php echo Session::get(SESSION_PREFIX.'cashRegisterId'); ?>',
    tempInvKeyField = '<?php echo Config::getFromCacheDefault('CONFIG_POS_TEMP_INVOICE_KEY_FIELD', null, '') ?>',
    isConfigServiceJobAccompany = <?php echo $this->isConfigServiceJobAccompany; ?>,
    isConfigAccompanyItem = <?php echo $this->isConfigAccompanyItem; ?>,
    posOrderTimer = <?php echo $this->posOrderTimer; ?>,
    posCashierInsertC1 = <?php echo $this->cashierInsertC1; ?>,
    posRemainderCoupon = <?php echo $this->remainderCoupon; ?>,
    posServiceRowPriceEdit = <?php echo $this->posServiceRowPriceEdit; ?>,
    posMatrixHideSale = <?php echo $this->matrixHideSale; ?>,
    posChooseReturn = <?php echo $this->posChooseReturn; ?>,
    isIpad = <?php echo $this->isIpad; ?>,
    isReturnValueZero = '<?php echo $this->isReturnValueZero; ?>',
    isConfigBankBilling = '<?php echo $this->isConfigBankBilling; ?>',
    isRequiredJobDelivery = '<?php echo $this->isRequiredJobDelivery; ?>',
    isEditCustomerInfoBook = '<?php echo $this->isEditCustomerInfoBook; ?>',
    limitBonusAmount = '<?php echo json_encode($this->limitBonusAmount); ?>',
    limitBonusAmount = limitBonusAmount ? JSON.parse(limitBonusAmount) : '',
    selectedItemId = '<?php echo issetParam($this->selectedItemId); ?>',
    minItemQty = <?php echo Config::getFromCacheDefault('POS_MIN_ITEM_QTY', null, '0') ?>,
    POS_FILL_CASH_AMOUNT_PAYMENT = <?php echo Config::getFromCacheDefault('POS_FILL_CASH_AMOUNT_PAYMENT', null, '0') ?>,
    POS_PAY_BASKET_LIST = '<?php echo Config::getFromCacheDefault('POS_PAY_BASKET_LIST', null, '') ?>',
    posCheckZBpassword = '<?php echo Config::getFromCacheDefault('POS_CHECK_ZB_PASSWORD', null, '') ?>',
    posTypeCode = '<?php echo Session::get(SESSION_PREFIX.'posTypeCode'); ?>',
    isPosSejim = '<?php echo Config::getFromCacheDefault('POS_PAY_LEFT_SIDE_SHOW_LEAD', null, '') ?>',
    selectedCustomerId = '<?php echo issetParam($this->selectedCustomerId); ?>';
    <?php if (Session::get(SESSION_PREFIX.'isEditBasketPrice') === '1') { ?>
        var posIsEditBasketPrice = true;
    <?php } ?>    
</script>    