<?php
 
class PermissionTableSeeder extends Seeder {
 
public function run()
{
// Delete any content in the roles table
	Eloquent::unguard();

	DB::table('permissions')->delete();
	 
	$permissions_data = array(
	array('name' => "manage_system", 'display_name' => "Manage System"),
	array('name' => "mib1_manage_rfp", 'display_name' => "MIB 1-Request for Payment"),
	array('name' => "mib1_manage_apv", 'display_name' => "MIB 1-AP Voucher"),
	array('name' => "mib1_manage_cv", 'display_name' => "MIB 1-Cheque Voucher"),
	array('name' => "mib1_approve_rfp", 'display_name' => "MIB 1-Approve Request for Payment"),
	array('name' => "mib1_approve_apv", 'display_name' => "MIB 1-Approve AP Voucher"),
	array('name' => "mib1_approve_cv", 'display_name' => "MIB 1-Approve Cheque Voucher"),
	array('name' => "mib1_access", 'display_name' => "MIB 1 Access"),
	array('name' => "mib2_manage_rfp", 'display_name' => "MIB 2-Request for Payment"),
	array('name' => "mib2_manage_apv", 'display_name' => "MIB 2-Voucher"),
	array('name' => "mib2_manage_cv", 'display_name' => "MIB 2-Cheque Voucher"),
	array('name' => "mib2_approve_rfp", 'display_name' => "MIB 2-Approve Request for Payment"),
	array('name' => "mib2_approve_apv", 'display_name' => "MIB 2-Approve AP Voucher"),
	array('name' => "mib2_approve_cv", 'display_name' => "MIB 2-Approve Cheque Voucher"),
	array('name' => "mib2_access", 'display_name' => "MIB 2 Access"),
	array('name' => "csisi_manage_rfp", 'display_name' => "CSISI-Request for Payment"),
	array('name' => "csisi_manage_apv", 'display_name' => "CSISI-AP Voucher"),
	array('name' => "csisi_manage_cv", 'display_name' => "CSISI-Cheque Voucher"),
	array('name' => "csisi_approve_rfp", 'display_name' => "CSISI-Approve Request for Payment"),
	array('name' => "csisi_approve_apv", 'display_name' => "CSISI-Approve AP Voucher"),
	array('name' => "csisi_approve_cv", 'display_name' => "CSISI-Approve Cheque Voucher"),
	array('name' => "csisi_access", 'display_name' => "CSISI Access"),
	array('name' => "mibstc_manage_rfp", 'display_name' => "MIBSTC-Request for Payment"),
	array('name' => "mibstc_manage_apv", 'display_name' => "MIBSTC-AP Voucher"),
	array('name' => "mibstc_manage_cv", 'display_name' => "MIBSTC-Cheque Voucher"),
	array('name' => "mibstc_approve_rfp", 'display_name' => "MIBSTC-Approve Request for Payment"),
	array('name' => "mibstc_approve_apv", 'display_name' => "MIBSTC-Approve AP Voucher"),
	array('name' => "mibstc_approve_cv", 'display_name' => "MIBSTC-Approve Cheque Voucher"),
	array('name' => "mibstc_access", 'display_name' => "MIBSTC Access")
	);
	
	foreach ($permissions_data AS $permission) {
		Permission::create($permission);
		}
	

	$role = Role::where('name','=', 'Administrator')->first();
	if($role->id){
		//try{
            $role->perms()->sync(array(Permission::where('name','=','manage_system')->first()->id));
        //}catch(Exception $e){
                //$e->getMessage();
        //}
	}
}
 
}