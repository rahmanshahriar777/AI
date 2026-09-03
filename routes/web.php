<?php

use App\Http\Controllers\MailTemplateController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BusinessInfoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\FjobController;
use App\Http\Controllers\FjobProgressController;
use App\Http\Controllers\GlobalController;
use App\Http\Controllers\HSController;
use App\Http\Controllers\HsToolController;
use App\Http\Controllers\JobAttributeController;
use App\Http\Controllers\JobTypeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\QuotationTemplateController;
use App\Http\Controllers\SchedulerController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StockAttributeController;
use App\Http\Controllers\StockCategoryController;
use App\Http\Controllers\StockItemController;
use App\Http\Controllers\StockWarehouseController;
use App\Http\Controllers\VehicleCategoryController;
use App\Http\Controllers\VehicleCheckingController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleOverviewController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\GeminiAIController;

Route::get('/', function () {
    return Redirect::to('/login');
});

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/query', [GlobalController::class, 'query'])->name('query');
Route::get('/checkpostcode/{key}', [GlobalController::class, 'checkPostCode'])->name('query.checkpostcode');
Route::get('/checkposttown/{key}', [GlobalController::class, 'checkPostTown'])->name('query.checkposttown');
Route::post('/submit-query', [GlobalController::class, 'storeQuery'])->name('query.submit');

Route::get('/login-as-user/{id?}', [GlobalController::class, 'loginAsUser'])->name('login.as.user');
Route::get('/query-test', [GlobalController::class, 'queryTest'])->name('query.test');

Route::middleware(['auth'])->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('users', UserController::class);
    Route::resource('mailtemplates', MailTemplateController::class);
    Route::get('/mail-template/{id}/{rel_type?}/{rel_id?}', [MailTemplateController::class, 'showmailtemplate'])->name('mailtemplate.showmail');
    Route::get('/myprofile', [UserController::class, 'myprofile'])->name('users.myprofile');

    Route::resource('businessinfo', BusinessInfoController::class);
    // Route::get('/businessinfo/edit/{id}','BusinessInfoController@edit');
    // Route::get('/businessinfo/create','BusinessInfoController@create');
    //Route::post('/businessinfo/updatefinancialdetails/{id}', [BusinessInfoController::class, 'updateFinancialDetails'])->name('businessinfo.updatefinancialdetails');
    Route::post('/businessinfo/upload-brand-files/{id}',[BusinessInfoController::class, 'uploadBrandFiles'])->name('businessinfo.uploadbrandfiles');

    Route::resource('enquiries', EnquiryController::class);
    Route::get('enquiry/inprogress', [EnquiryController::class, 'showInprogress'])->name('enquiry.show.inprogress');
    Route::get('enquiry/completed', [EnquiryController::class, 'showCompleted'])->name('enquiry.show.completed');
    Route::get('enquiry/rejected', [EnquiryController::class, 'showRejected'])->name('enquiry.show.rejected');
    Route::post('/enquiry/updatedescription/{id}', [EnquiryController::class, 'updateEnquiryDetails'])->name('enquiry.updatedescription');
    Route::post('/enquiry/updateaddress/{id}', [EnquiryController::class, 'updateEnquiryAddress'])->name('enquiry.updateaddress');
    Route::post('/enquiry/updatestatus/{id}', [EnquiryController::class, 'updateEnquiryStatus'])->name('enquiry.updatestatus');
    Route::post('enquiry/otheroptions/{id}', [EnquiryController::class, 'updateEnquiryOtherOptions'])->name('enquiry.otheroptions');

    Route::post('enquiries/add-note/{enquiry_id}', [EnquiryController::class, 'enqueryNotes'])->name('enquiry.add-note');
    Route::delete('enquiries/delete-note/{id}', [EnquiryController::class, 'deleteenqueryNotes'])->name('enquiry.destroynote');

    Route::post('enquiries/add-images', [EnquiryController::class, 'enqueryImages'])->name('enquiry.add-images');
    Route::delete('enquiries/delete-image/{id}', [EnquiryController::class, 'destroyEnqueryImage'])->name('enquiry.destroy-image');

    Route::post('enquiry-to-lead-conversion', [EnquiryController::class, 'enquerytoLeadConversion'])->name('enquiry.leadconversion');

    Route::resource('customers', CustomerController::class);
    Route::post('/customers/{id}/update-basic-info', [CustomerController::class, 'updateBasicInfo']);
    Route::post('/customer/storeaddress/{id}', [CustomerController::class, 'storeCustomerAddress'])->name('customer.storeaddress');
    Route::delete('/customer/deleteaddress/{id}', [CustomerController::class, 'deleteCustomerAddress'])->name('customer.deleteaddress');
    Route::post('/customer/address-update/{id}', [CustomerController::class, 'updateCustomerAddress'])->name('customer.address-update');
    Route::post('/customer/address-delete/{id}', [CustomerController::class, 'deleteCustomerAddress'])->name('customer.address-delete');
    Route::post('/customer/updatefinancialdetails/{id}', [CustomerController::class, 'updateFinancialDetails'])->name('customer.updatefinancialdetails');
    Route::get('customer/address-get/{id}', [CustomerController::class, 'getCustomerAddress'])->name('customer.address-get');
    Route::get('customer/archived', [CustomerController::class, 'showArchived'])->name('customer.show.archived');

    Route::resource('leads', LeadController::class);
    Route::get('lead/completed', [LeadController::class, 'showCompleted'])->name('lead.show.completed');
    Route::get('lead/rejected', [LeadController::class, 'showRejected'])->name('lead.show.rejected');
    Route::get('lead/archived', [LeadController::class, 'showArchived'])->name('lead.show.archived');
    Route::get('lead/inprogress', [LeadController::class, 'showInprogress'])->name('lead.show.inprogress');
    Route::post('lead/updatedescription/{id}', [LeadController::class, 'updateLeadDetails'])->name('leads.updatedescription');
    Route::post('/leads/updateaddress/{id}', [LeadController::class, 'updateLeadAddress'])->name('leads.updateaddress');
    Route::post('/leads/updatestatus/{id}', [LeadController::class, 'updateLeadStatus'])->name('leads.updatestatus');
    Route::post('lead/otheroptions/{id}', [LeadController::class, 'updateLeadOtherOptions'])->name('lead.otheroptions');

    Route::post('lead-to-job-conversion', [LeadController::class, 'leadtoJobConversion'])->name('lead.jobconversion');

    Route::post('lead/add-note/{leadid}', [LeadController::class, 'leadNotes'])->name('leads.add-note');
    Route::delete('lead/delete-note/{id}', [LeadController::class, 'deleteLeadNotes'])->name('leads.destroynote');

    Route::post('leads/add-images', [LeadController::class, 'addLeadImages'])->name('leads.add-images');
    Route::delete('leads/delete-image/{id}', [LeadController::class, 'destroyLeadImage'])->name('leads.destroy-image');

    Route::post('leads/attachments-upload', [LeadController::class, 'uploadLeadAttachments'])->name('leads.attachments-upload');
    Route::delete('leads/delete-attachment/{id}', [LeadController::class, 'destroyLeadAttachment'])->name('leads.destroy-attachment');

    //Route::get('leads/{id}/quotations', [LeadController::class, 'show'])->name('lead.show.quotations');
    Route::get('leads/{id}/{viewwith?}', [LeadController::class, 'show'])
    ->where('viewwith', 'quotations')
    ->name('lead.show.quotations');

    Route::resource('jobtypes', JobTypeController::class);

    Route::resource('quotationtemplates', QuotationTemplateController::class);
    Route::post('quotationtemplate/addsection/', [QuotationTemplateController::class, 'addSection'])->name('quotationtemplate.add-section');
    Route::post('/quotationtemplate/section/populate', [QuotationTemplateController::class, 'populateSections'])->name('quotationtemplate.section-populate');
    Route::delete('quotationtemplate/deletesection/{id}', [QuotationTemplateController::class, 'deleteSection'])->name('quotationtemplate.delete-section');

    Route::get('lead-quotations/{leadid}', [QuotationController::class, 'index'])->name('quotations.lead');
    Route::get('quotation/{id}', [QuotationController::class, 'show'])->name('quotations.show');
    Route::get('quotation/create/{leadid}/{templateid}', [QuotationController::class, 'create'])->name('quotations.create');
    Route::post('quotation/store', [QuotationController::class, 'store'])->name('quotations.store');
    Route::get('quotation/edit/{id}', [QuotationController::class, 'edit'])->name('quotations.edit');
    Route::post('quotation/update/{id}', [QuotationController::class, 'update'])->name('quotations.update');
    Route::delete('quotation/destroy/{id}', [QuotationController::class, 'destroy'])->name('quotations.destroy');

    Route::post('quotation/updatestatus/{id}', [QuotationController::class, 'updateQuotationStatus'])->name('quotations.updatestatus');

    Route::post('quotationsections/reorder', [QuotationController::class, 'reorderQuotationSections'])->name('quotationsections.reorder');
    Route::post('quotationsections/update/{id}', [QuotationController::class, 'updateQuotationSectionsContent'])->name('quotationsections.update');
    Route::post('quotationsections/upload-attachments/{id}', [QuotationController::class, 'uploadQuotationSectionsAttachments'])->name('quotationsections.upload-attachments');
    Route::delete('quotationsections/delete-attachments/{id}', [QuotationController::class, 'destroyQuotationSectionsAttachments'])->name('quotationsections.destroy-attachments');

    Route::get('quotation/pdf/{quotationId}', [QuotationController::class, 'downloadQuotationPdf'])->name('quotations.download-pdf');

    Route::get('jobs', [FjobController::class, 'index'])->name('job.index');
    Route::get('jobs/completed', [FjobController::class, 'showCompletedJobs'])->name('job.show.completed');
    Route::get('jobs/rejected', [FjobController::class, 'showRejectedJobs'])->name('job.show.rejected');

    Route::get('job/{id}', [FjobController::class, 'show'])->name('job.show');
    Route::post('job/updatedescription/{id}', [FjobController::class, 'updateJobDetails'])->name('job.updatedescription');
    Route::post('job/updateaddress/{id}', [FjobController::class, 'updateJobAddress'])->name('job.updateaddress');
    Route::post('job/updatestatus/{id}', [FjobController::class, 'updateJobStatus'])->name('job.updatestatus');
    Route::post('job/updatetitle/{id}', [FjobController::class, 'updateJobTitle'])->name('job.updatetitle');
    Route::post('job/updatetype/{id}', [FjobController::class, 'updateJobType'])->name('job.updatetype');
    Route::post('job/otheroptions/{id}', [FjobController::class, 'updateJobOtherOptions'])->name('job.otheroptions');

    Route::post('job/add-note/{jobid}', [FjobController::class, 'jobNotes'])->name('job.add-note');
    Route::delete('job/delete-note/{id}', [FjobController::class, 'deleteJobNotes'])->name('job.destroynote');

    Route::post('job/add-images', [FjobController::class, 'addJobImages'])->name('job.add-images');
    Route::delete('job/delete-image/{id}', [FjobController::class, 'destroyJobImage'])->name('job.destroy-image');

    Route::post('job/attachments-upload', [FjobController::class, 'uploadJobAttachments'])->name('job.attachments-upload');
    Route::delete('job/delete-attachment/{id}', [FjobController::class, 'destroyJobAttachment'])->name('job.destroy-attachment');

    Route::get('job-add-attributes/{jobid}', [FjobController::class, 'jobAddAttributes'])->name('job-attributes.add');
    Route::post('job-add-attributes/{jobid}/store', [FjobController::class, 'storeJobAddAttributes'])->name('job-attributes.add-store');

    Route::get('scheduler', [SchedulerController::class, 'index'])->name('scheduler.index');
    Route::post('scheduler/workers-calendar', [SchedulerController::class, 'getSchedulerCalendarData'])->name('scheduler.workers-calendar');
    Route::get('scheduler/job-schedule/{id}', [SchedulerController::class, 'showSchedule'])->name('scheduler.job-schedule');

    Route::post('scheduler/fake-workers-calendar', [SchedulerController::class, 'getFakeSchedulerCalendarData'])->name('scheduler.fake-workers-calendar');
    Route::get('scheduler/fake-job-schedule/{id}', [SchedulerController::class, 'showFakeSchedule'])->name('scheduler.fake-job-schedule');

    Route::post('scheduler/store-job-schedule', [SchedulerController::class, 'storeJobSchedule'])->name('scheduler.store-job-schedule');
    Route::put('scheduler/update-job-schedule/{id}', [SchedulerController::class, 'updateJobSchedule'])->name('scheduler.update-job-schedule');
    Route::delete('scheduler/delete-schedule/{id}', [SchedulerController::class, 'deleteSchedule'])->name('scheduler.delete-schedule');

    Route::post('scheduler/store-fake-job-schedule', [SchedulerController::class, 'storeFakeJobSchedule'])->name('scheduler.store-fake-job-schedule');
    Route::put('scheduler/update-fake-job-schedule/{id}', [SchedulerController::class, 'updateFakeJobSchedule'])->name('scheduler.update-fake-job-schedule');
    Route::delete('scheduler/delete-fake-schedule/{id}', [SchedulerController::class, 'deleteFakeSchedule'])->name('scheduler.delete-fake-schedule');

    Route::get('job-attributes', [JobAttributeController::class, 'index'])->name('job-attributes.index');
    Route::post('job-attributes/store', [JobAttributeController::class, 'storeJobAttribute'])->name('job-attributes.store');
    Route::get('job-attributes/{id}/edit', [JobAttributeController::class, 'editJobAttribute'])->name('job-attributes.edit');
    Route::put('job-attributes/{id}/update', [JobAttributeController::class, 'updateJobAttribute'])->name('job-attributes.update');

    Route::post('job-attributes/{jobAttribute}/storedetails', [JobAttributeController::class, 'storeAttributeDetails'])->name('job-attributes.store-details');
    Route::put('job-attributes/{jobAttributedetails}/updatedetails', [JobAttributeController::class, 'updateAttributeDetails'])->name('job-attributes.update-details');

    Route::get('stockwarehouses', [StockWarehouseController::class, 'index'])->name('stockwarehouses.index');
    Route::post('stockwarehouses/store', [StockWarehouseController::class, 'storeWarehouse'])->name('stockwarehouses.store');
    Route::get('stockwarehouses/{id}', [StockWarehouseController::class, 'showWarehouse'])->name('stockwarehouses.show');
    Route::put('stockwarehouses/{id}/update', [StockWarehouseController::class, 'updateWarehouse'])->name('stockwarehouses.update');
    Route::delete('stockwarehouses/{id}', [StockWarehouseController::class, 'destroyWarehouse'])->name('stockwarehouses.destroy');

    Route::get('stockcategories', [StockCategoryController::class, 'index'])->name('stockcategories.index');
    Route::post('stockcategories/stote', [StockCategoryController::class, 'storeCategory'])->name('stockcategories.store');
    Route::get('stockcategories/{id}', [StockCategoryController::class, 'showCategory'])->name('stockcategories.show');
    Route::put('stockcategories/{id}/update', [StockCategoryController::class, 'updateCategory'])->name('stockcategories.update');
    Route::delete('stockcategories/{id}', [StockCategoryController::class, 'destroyCategory'])->name('stockcategories.destroy');

    Route::get('stockattributes', [StockAttributeController::class, 'index'])->name('stockattributes.index');
    Route::post('stockattributes/store', [StockAttributeController::class, 'storeAttribute'])->name('stockattributes.store');
    Route::get('stockattributes/{id}', [StockAttributeController::class, 'showAttribute'])->name('stockattributes.edit');
    Route::put('stockattributes/{id}/update', [StockAttributeController::class, 'updateAttribute'])->name('stockattributes.update');
    Route::delete('stockattributes/{id}', [StockAttributeController::class, 'destroyAttribute'])->name('stockattributes.destroy');

    Route::get('stockitems', [StockItemController::class, 'index'])->name('stockitems.index');
    Route::post('stockitems/store', [StockItemController::class, 'storeItem'])->name('stockitems.store');
    Route::get('stockitems/{id}', [StockItemController::class, 'showItem'])->name('stockitems.show');
    Route::put('stockitems/{id}/update', [StockItemController::class, 'updateItem'])->name('stockitems.update');
    Route::delete('stockitems/{id}', [StockItemController::class, 'destroyItem'])->name('stockitems.destroy');
    Route::post('stockitems/add-attribute', [StockItemController::class, 'addAttributeToItem'])->name('stockitems.add_attribute');
    Route::post('stockitems/{stockItem}/add-variants', [StockItemController::class, 'addItemVariants'])->name('stockitems.add_variant');

    Route::get('stockitemsinventory', [StockItemController::class, 'indexInventory'])->name('stockitemsinventory.index');
    Route::post('stockitemsinventory/add-stock', [StockItemController::class, 'addStockToInventory'])->name('stockitemsinventory.add-stock');
    Route::get('stockitemsinventory/{id}', [StockItemController::class, 'showStockVarient'])->name('stockitemsinventory.show-stockvarient');
    Route::post('stockitemsinventory/transfer-stock', [StockItemController::class, 'transferStock'])->name('stockitemsinventory.transfer-stock');
    Route::get('stockitemsinventory/{id}/history', [StockItemController::class, 'stockInventoryMovementHistory'])->name('stockitemsinventory.movement-history');

    Route::get('stockitemrequisitions', [StockItemController::class, 'indexRequisitions'])->name('stockitemrequisitions.index');
    Route::post('stockitemrequisitions/store-primary', [StockItemController::class, 'storeRequisitionPrimary'])->name('stockitemrequisitions.store-primary');
    Route::post('stockitemrequisitions/store-details', [StockItemController::class, 'storeRequisitionDetails'])->name('stockitemrequisitions.store-details');
    Route::get('stockitemrequisitions/{id}', [StockItemController::class, 'showRequisitionDetails'])->name('stockitemrequisitions.show');
    Route::delete('stockitemrequisitions/details/{id}', [StockItemController::class, 'destroyRequisitionDetails'])->name('stockitemrequisitions.destroy-details');

    Route::get('vehiclecategories', [VehicleCategoryController::class, 'index'])->name('vehiclecategories.index');
    Route::post('vehiclecategories/store', [VehicleCategoryController::class, 'store'])->name('vehiclecategories.store');
    Route::get('vehiclecategories/{id}', [VehicleCategoryController::class, 'show'])->name('vehiclecategories.show');
    Route::put('vehiclecategories/{id}/update', [VehicleCategoryController::class, 'update'])->name('vehiclecategories.update');
    Route::delete('vehiclecategories/{id}', [VehicleCategoryController::class, 'destroy'])->name('vehiclecategories.destroy');

    Route::get('vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create');
    Route::post('vehicles/store', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::get('vehicle/{key}', [VehicleController::class, 'show'])->name('vehicles.show');
    Route::get('vehicles/{id}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
    Route::put('vehicles/{id}/update', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('vehicles/{id}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');

    Route::get('drivers', [DriverController::class, 'index'])->name('drivers.index');
    Route::get('drivers/create', [DriverController::class, 'create'])->name('drivers.create');
    Route::post('drivers/store', [DriverController::class, 'store'])->name('drivers.store');
    Route::get('drivers/{id}', [DriverController::class, 'show'])->name('drivers.show');
    Route::get('drivers/{id}/edit', [DriverController::class, 'edit'])->name('drivers.edit');
    Route::put('drivers/{id}/update', [DriverController::class, 'update'])->name('drivers.update');
    Route::delete('drivers/{id}', [DriverController::class, 'destroy'])->name('drivers.destroy');

    Route::post('drivers/assign-vehicle', [DriverController::class, 'assignVehicle'])->name('drivers.assign-vehicle');
    Route::post('drivers/unassign-vehicle/{key}', [DriverController::class, 'unassignVehicle'])->name('drivers.unassign-vehicle');

    Route::get('vehicle-checkings', [VehicleCheckingController::class, 'indexVehicleCheckingCategories'])->name('vehicle-checkings.index-categories');
    Route::post('vehicle-checkings/store', [VehicleCheckingController::class, 'storeVehicleCheckingCategories'])->name('vehicle-checkings.store-categories');

    Route::get('vehicle-checkings/checklists/{vehicle_checking_category_id}', [VehicleCheckingController::class, 'indexVehicleCheckingChecklists'])->name('vehicle-checkings.index-checklists');
    Route::post('vehicle-checkings/checklists/store', [VehicleCheckingController::class, 'storeVehicleCheckingChecklists'])->name('vehicle-checkings.store-checklists');
    Route::get('vehicle-checkings/checklistshow/{id}', [VehicleCheckingController::class, 'showVehicleCheckingChecklists'])->name('vehicle-checkings.show-checklists');
    Route::put('vehicle-checkings/checklists/{id}/update', [VehicleCheckingController::class, 'updateVehicleCheckingChecklists'])->name('vehicle-checkings.update-checklists');
    Route::delete('vehicle-checkings/checklists/{id}', [VehicleCheckingController::class, 'destroyVehicleCheckingChecklists'])->name('vehicle-checkings.destroy-checklists');

    Route::post('vehicle-checkings/checklist-attributes/store', [VehicleCheckingController::class, 'storeVehicleCheckingChecklistAttributes'])->name('vehicle-checkings.store-checklist-attributes');
    Route::get('vehicle-checkings/checklist-attributes/{id}', [VehicleCheckingController::class, 'showVehicleCheckingChecklistAttributes'])->name('vehicle-checkings.show-checklist-attributes');
    Route::put('vehicle-checkings/checklist-attributes/{id}/update', [VehicleCheckingController::class, 'updateVehicleCheckingChecklistAttributes'])->name('vehicle-checkings.update-checklist-attributes');
    Route::delete('vehicle-checkings/checklist-attributes/{id}', [VehicleCheckingController::class, 'destroyVehicleCheckingChecklistAttributes'])->name('vehicle-checkings.destroy-checklist-attributes');

    Route::get('vehicle-overviews/{vehicle_id}', [VehicleOverviewController::class, 'index'])->name('vehicle-overviews.index');
    Route::get('vehicle-overviews/create/{vehicle_id}', [VehicleOverviewController::class, 'create'])->name('vehicle-overviews.create');
    Route::post('vehicle-overviews/store', [VehicleOverviewController::class, 'store'])->name('vehicle-overviews.store');
    Route::get('vehicle-overviews/show/{id}', [VehicleOverviewController::class, 'vehicleOverviewShow'])->name('vehicle-overviews.show');
    Route::put('vehicle-overviews/update/{id}', [VehicleOverviewController::class, 'update'])->name('vehicle-overviews.update');
    Route::delete('vehicle-overviews/destroy/{id}', [VehicleOverviewController::class, 'destroy'])->name('vehicle-overviews.destroy');
    Route::get('vehicle-overviews/export/{vehicle_id}', [VehicleOverviewController::class, 'export'])->name('vehicle-overviews.export');

    Route::get('job-overviews', [FjobProgressController::class, 'index'])->name('job-overviews.index');
    Route::post('job-overviews/store', [FjobProgressController::class, 'store'])->name('job-overviews.store');
    Route::get('job-overviews/show/{jobprogress_id}', [FjobProgressController::class, 'jobOverviewsShow'])->name('job-overviews.show');
    Route::get('jobprogress/{id}/export-gantt', [FjobProgressController::class, 'exportGantt'])->name('job-overviews.export-gantt');

    Route::post('job-overviews/store-task', [FjobProgressController::class, 'storeTask'])->name('job-overviews.store-task');
    Route::get('job-overviews/show-task/{task_id}', [FjobProgressController::class, 'showTask'])->name('job-overviews.show-task');
    Route::put('job-overviews/update-task', [FjobProgressController::class, 'updateTask'])->name('job-overviews.update-task');

    Route::delete('job-overviews/destroy-task/{task_id}', [FjobProgressController::class, 'destroyTask'])->name('job-overviews.destroy-task');

    Route::get('hs-checklists', [HSController::class, 'indexHsChecklists'])->name('hs-checklists.index');
    Route::post('hs-checklists/store', [HSController::class, 'storeHsChecklists'])->name('hs-checklists.store');
    Route::get('hs-checklists/{id}', [HSController::class, 'showHsChecklists'])->name('hs-checklists.show');
    Route::put('hs-checklists/{id}/update', [HSController::class, 'updateHsChecklists'])->name('hs-checklists.update');
    Route::delete('hs-checklists/{id}', [HSController::class, 'destroyHsChecklists'])->name('hs-checklists.destroy');

    Route::get('fjob-hs-check', [HSController::class, 'indexFjobHsCheck'])->name('fjob-hs-check.index');
    Route::get('fjob-hs-check/create', [HSController::class, 'createFjobCheck'])->name('fjob-hs-check.create');
    Route::post('fjob-hs-check/store', [HSController::class, 'storeFjobHsCheck'])->name('fjob-hs-check.store');
    Route::get('fjob-hs-check/{id}', [HSController::class, 'showFjobHsCheck'])->name('fjob-hs-check.show');
    Route::put('fjob-hs-check/{id}/update', [HSController::class, 'updateFjobHsCheck'])->name('fjob-hs-check.update');

    Route::get('hs-tools', [HsToolController::class, 'indexHsTools'])->name('hs-tools.index');
    Route::post('hs-tools/store', [HsToolController::class, 'storeHsTools'])->name('hs-tools.store');
    Route::put('hs-tools/{id}/update', [HsToolController::class, 'updateHsTools'])->name('hs-tools.update');
    Route::get('hs-tools/{id}', [HsToolController::class, 'showHsTools'])->name('hs-tools.show');
    Route::get('hs-tools/{id}/checkup', [HsToolController::class, 'createHsToolsCheck'])->name('hs-tools.createcheck');
    Route::post('hs-tools/{id}/checkup', [HsToolController::class, 'storeHsToolsCheck'])->name('hs-tools.storecheck');
    Route::get('hs-tools/{id}/checkup/{checkup_id}', [HsToolController::class, 'showHsToolsCheck'])->name('hs-tools.showcheck');
    Route::put('hs-tools/{checkup_id}/checkupdate', [HsToolController::class, 'updateHsToolsCheck'])->name('hs-tools.updatecheck');
    Route::delete('hs-tools/{id}', [HsToolController::class, 'destroyHsTools'])->name('hs-tools.destroy');

    Route::get('tasks', [StaffController::class, 'indexTasks'])->name('tasks.index');
    Route::get('tasks/show/{task_id}', [StaffController::class, 'showTasks'])->name('tasks.show');
    Route::put('tasks/update', [StaffController::class, 'updateTasks'])->name('tasks.update');

    Route::get('certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('certificates/create', [CertificateController::class, 'create'])->name('certificates.create');
    Route::post('certificates/store', [CertificateController::class, 'store'])->name('certificates.store');
    Route::get('certificates/{id}', [CertificateController::class, 'show'])->name('certificates.show');
    Route::get('certificates/{id}/edit', [CertificateController::class, 'edit'])->name('certificates.edit');
    Route::put('certificates/{id}/update', [CertificateController::class, 'update'])->name('certificates.update');
    Route::delete('certificates/{id}', [CertificateController::class, 'destroy'])->name('certificates.destroy');

    // Google Gemini AI Workflow & Assistant Routes
    Route::get('/gemini', [GeminiAIController::class, 'index'])->name('gemini.index');
    Route::post('/gemini/test-connection', [GeminiAIController::class, 'testConnection'])->name('gemini.test');
    Route::post('/gemini/save-settings', [GeminiAIController::class, 'saveSettings'])->name('gemini.save-settings');
    Route::post('/gemini/analyze-lead', [GeminiAIController::class, 'analyzeLead'])->name('gemini.analyze-lead');
    Route::post('/gemini/draft-email', [GeminiAIController::class, 'draftEmail'])->name('gemini.draft-email');
    Route::post('/gemini/generate-scope', [GeminiAIController::class, 'generateQuotationScope'])->name('gemini.generate-scope');
    Route::post('/gemini/generate-safety', [GeminiAIController::class, 'generateSafetyChecklist'])->name('gemini.generate-safety');
    Route::post('/gemini/chat', [GeminiAIController::class, 'chat'])->name('gemini.chat');
});
