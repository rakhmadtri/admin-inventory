<?php
defined('BASEPATH') or exit('No direct script access allowed');
/*
 * Controller Template ver 0.2 
 * To use just search change start change end comments
 * Add your code inside these tags
 */
class Inventory_adjustment extends CI_Controller
{

    var $data;

    var $roles;

    var $table;

    var $pfield;

    var $module;

    var $modules;

    var $logfield;

    var $module_path;

    var $module_label;

    var $controller_page;

    public function __construct()
    {
        parent::__construct();
        //check if you are using $record or $records, just replace all
        $this->load->model('generic_model', 'record');

        //change start
        //parent module, controller, table, pfield, logfield
        $this->_init_setup('Inventory', 'inventory_adjustment', 'inventory_adjustments', 'adjID', 'adjNo');
        //change end
    }

    private function _init_setup($module='',  $controller='', $table='', $pfield='', $logfield='')
    {
        $this->module = $module;
        $this->data['controller_page'] = $this->controller_page = site_url($controller);
        $this->table = $table;
        $this->pfield = $this->data['pfield'] = $pfield; //defines primary key
        $this->logfield = $logfield; // for record logs
        $this->module_path = 'modules/' . strtolower(str_replace(" ", "_", $this->module)) . '/' . $controller; // defines module path
        $this->module_label = ucfirst(str_replace("_", " ", $controller));
        $this->data['controller_name'] = $controller;
        $this->data['table_name'] = $table;
                                                                                                  
        // check for maintenance period
        if ($this->config_model->getConfig('Maintenance Mode') == '1') {
            header('location: ' . site_url('maintenance_mode'));
        }
        
        // check user session
        if (! $this->session->userdata('current_user')->sessionID) {
            header('location: ' . site_url('login'));
        }
    }

    private function submenu()
    {
        // submenu setup
        require_once ('modules.php');
        
        //load each module metadata
        foreach ($modules as $mod) {
            $this->load->view('modules/' . str_replace(" ", "_", strtolower($mod)) . '/metadata');
        }
        
        $this->data['modules'] = $this->modules;
        $this->data['current_main_module'] = $this->modules[$this->module]['main']; // defines the current main module
        //change start
        $this->data['current_module'] = $this->modules[$this->module]['sub']['Inventory Adjustment']; // defines the current sub module
        //change end
                                                                                                  
        // check roles
        $this->check_roles();
        $this->data['roles'] = $this->roles;
    }

    private function check_roles()
    {
        // check roles
        $this->roles['create']  = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Add '.$this->data['current_module']['module_label']);
        $this->roles['view']    = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View '.$this->data['current_module']['module_label']);
        $this->roles['edit']    = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Edit '.$this->data['current_module']['module_label']);
        $this->roles['delete']  = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Delete '.$this->data['current_module']['module_label']);
        $this->roles['confirm']  = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Confirm '.$this->data['current_module']['module_label']);
        $this->roles['cancel']  = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Cancel '.$this->data['current_module']['module_label']);
    }

    private function _in_used($id = 0)
    {
        //change start
        $tables = array(
            'inventory_adjustments' => 'adjID'
        );
        //change end
        
        if (! empty($tables)) {
            foreach ($tables as $table => $fld) {
                $this->db->where($fld, $id);
                if ($this->db->count_all_results($table)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function index()
    {
        $this->show();
    }

    public function create()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        // check roles
        if ($this->roles['create']) {

            //change start
            //change end

            $series = 0;
            $ancillaryID = $this->session->userdata('current_user')->ancillaryID;
            $this->db->like('adjNo', 'ADJ-'.$ancillaryID.'-', 'both');
            $series = $this->db->count_all_results('inventory_adjustments');

            $data['series'] = 'ADJ-'.$ancillaryID.'-'.str_pad($series, 6 , "0", STR_PAD_LEFT);

            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path . '/create');
            $this->load->view('footer');
        } else {
            // no access this page
            $data['class'] = "danger";
            $data['msg'] = "Sorry, you don't have access to this page!";
            $data['urlredirect'] = "";
            $this->load->view('header', $data);
            $this->load->view('message');
            $this->load->view('footer');
        }
    }

 
    // adjID   adjNo   date    branchID    itemID  adjType DR: Debit; CR: Credit   qty remarks createdBy   confirmedBy dateConfirmed   cancelledBy dateCancelled   dateInserted    dateLastEdit    dateDeleted deletedBy   status
    public function save()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;

        $adjType = $this->input->post('adjType');
        $newQty = $this->input->post('qty');
        $expiry = $this->input->post('stockcard');
        $itemID = $this->input->post('itemID');
        $ancillaryID = $this->input->post('ancillaryID');

        //change start
        $table_fields = array('adjNo', 'ancillaryID', 'itemID', 'adjType', 'qty', 'remarks');
        //change end
        
        // check role
        if ($this->roles['create']) {
            $this->record->table = $this->table;
            $this->record->fields = array();
            
            foreach ($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }

            //change start
            $this->record->fields['date']    = date('Y-m-d', strtotime($this->input->post('date')));
            $this->record->fields['expiry']    = trim($this->input->post('stockcard'));
            $this->record->fields['createdBy']    = $this->session->userdata('current_user')->userID;
            $this->record->fields['dateInserted']= date('Y-m-d');
            //change end
            
            if ($this->record->save()) {
                $this->record->fields = array();
                //change start
                
                //change end
                $id = $this->record->where[$this->pfield] = $this->db->insert_id();
                $this->record->retrieve();

                //change start

                //change end



                
                // record logs
                $pfield = $this->pfield;
                
                $logs = "Record - " .$this->record->field->nodNo;
                $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->field->$pfield, 'Insert', $logs);
                
                $logfield = $this->pfield;
                
                // success msg
                $data["class"] = "success";
                $data["msg"] = $data['current_module']['module_label'] . " successfully saved.";
                $data["urlredirect"] = $this->controller_page . "/view/" . $this->encrypter->encode($id);
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            } else {
                // error
                $data["class"] = "danger";
                $data["msg"] = "Error in saving the " . $this->data['current_module']['module_label'] . "!";
                ;
                $data["urlredirect"] = "";
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            }
        } else {
            // error
            $data["class"] = "danger";
            $data["msg"] = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header", $data);
            $this->load->view("message");
            $this->load->view("footer");
        }
    }

    public function confirm_record($id, $status)
    {

        $this->submenu();
        $data = $this->data;
        // $status = $this->input->post('status');
        // $id = $this->input->post('id');


        if ($this->roles['confirm']) {

        

        if ($status == 2) {
            $this->db->set('status', 2);
            $this->db->set('dateConfirmed', date('Y-m-d'));
            $this->db->set('confirmedBy', $this->session->userdata('current_user')->userID);
            $this->db->where('adjID', $id);
            $this->db->update('inventory_adjustments');


            $this->db->where('adjID', $id);
            $item = $this->db->get('inventory_adjustments', 1)->row();

            $this->db->where('itemID', $item->itemID);
            $this->db->where('ancillaryID', $item->ancillaryID);
            $inventoryQty = $this->db->get('inventory', 1)->row()->qty;




            $debit = 0;
            $credit = 0;
            if ($item->adjType == 'DR') {
                $debit = $inventoryQty + $item->qty;

                $this->db->set('qty', $debit);

            } else {
                $credit = $inventoryQty  - $item->qty;

                $this->db->set('qty', $credit);
            }

            $this->db->where('itemID', $item->itemID);
            $this->db->where('ancillaryID', $item->ancillaryID);
            $this->db->update('inventory');

            $this->stockcard_model->qtyValue = $item->qty;
            $this->stockcard_model->refNo = $item->adjNo;
                //update stockcards
            
            if ($item->expiry == 'main') {
                $this->stockcard_model->update_single_main_stockcard($item->adjType, $item->itemID, $item->ancillaryID);
                    
            } else {
                $this->stockcard_model->update_single_xstockcard($item->adjType, $item->itemID, $item->ancillaryID, $item->expiry);
            }

            

            // successful
            $data["class"] = "success";
            $data["msg"] = $this->data['current_module']['module_label'] . " successfully updated.";
            $data["urlredirect"] = $this->controller_page . "/view/" . $this->encrypter->encode($id);
            $this->load->view("header", $data);
            $this->load->view("message");
            $this->load->view("footer");
        } else if ($status == 0) {
            $this->db->set('status', 0);
            $this->db->set('dateCancelled', date('Y-m-d'));
            $this->db->set('cancelledBy', $this->session->userdata('current_user')->userID);
            $this->db->where('adjID', $id);
            $this->db->update('inventory_adjustments');
            // successful
                $data["class"] = "success";
                $data["msg"] = $this->data['current_module']['module_label'] . " successfully cancelled.";
                $data["urlredirect"] = $this->controller_page . "/view/" . $this->encrypter->encode($id);
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
        }

    } else {
        // successful
        $data["class"] = "danger";
        $data["msg"] = "Sorry you don't have access to this page.";
        $data["urlredirect"] = $this->controller_page . "/view/" . $this->encrypter->encode($id);
        $this->load->view("header", $data);
        $this->load->view("message");
        $this->load->view("footer");
    }
        
    }


    public function edit($id)
    {   
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);
        
        if ($this->roles['edit']) {
            //change start
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->record->table = $this->table;
            // set fields for the current table
            $this->record->setFields();
            // extend fields - join tables                  
            // $this->record->fields[]  = 'job_titles.jobTitle';
            
            // set joins
            // $this->record->joins[]   = array('job_positions',$this->table.'.jobPositionID=job_positions.jobPositionID','left');
                
            // set where
            $this->record->where[$this->table.'.'.$this->pfield] = $id;
            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;
            //change end

            //change start
            //change end
            
            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path . '/edit');
            $this->load->view('footer');
        } else {
            // no access this page
            $data['class'] = "danger";
            $data['msg'] = "Sorry, you don't have access to this page!";
            $data['urlredirect'] = "";
            $this->load->view('header', $data);
            $this->load->view('message');
            $this->load->view('footer');
        }
    }

    public function update()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($this->input->post($this->pfield));
        //change start
        $table_fields = array('nodNo', 'fname', 'mname', 'gender', 'contactNo', 'streetNo', 'provinceID', 'cityID', 'barangayID', 'streetNo', 'isPatient', 'status');
        //change end
        
        // check roles
        if ($this->roles['edit']) {
            $this->record->table = $this->table;
            $this->record->fields = array();
            
            foreach ($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }

            //change start
            $this->record->fields['bday']    = date('Y-m-d', strtotime(trim($this->input->post('bday'))));
            $this->record->fields['dateLastEdit']= date('Y-m-d');
            //change end
            
            $this->record->pfield = $this->pfield;
            $this->record->pk = $id;
            
            // field logs here
            $wasChange = $this->log_model->field_logs($data['current_module']['module_label'], $this->table, $this->pfield, $id, 'Update', $this->record->fields);
            
            if ($this->record->update()) {
                //change start
                //change end

                // record logs
                if ($wasChange) {
                    $logs = "Record - " . trim($this->input->post($this->logfield));
                    $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->pk, 'Update', $logs);
                }
                
                // successful
                $data["class"] = "success";
                $data["msg"] = $this->data['current_module']['module_label'] . " successfully updated.";
                $data["urlredirect"] = $this->controller_page . "/view/" . $this->encrypter->encode($id);
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            } else {
                // error
                $data["class"] = "danger";
                $data["msg"] = "Error in updating the " . $this->data['current_module']['module_label'] . "!";
                $data["urlredirect"] = $this->controller_page . "/view/" . $this->record->pk;
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            }
        } else {
            // error
            $data["class"] = "danger";
            $data["msg"] = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header", $data);
            $this->load->view("message");
            $this->load->view("footer");
        }
    }

    public function delete($id)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);
        
        // check roles
        if ($this->roles['delete']) {
            // set fields
            $this->record->fields = array();
            // set table
            $this->record->table = $this->table;
            // set where
            $this->record->where[$this->pfield] = $id;
            // execute retrieve
            $this->record->retrieve();
            
            if (! empty($this->record->field)) {
                $this->record->pfield = $this->pfield;
                $this->record->pk = $id;
                
                // check if in used
                // if (! $this->_in_used($id)) {
                    if ($this->record->delete()) {
                        $logfield = $this->logfield;

                        //change start
                        //change end
                        
                        // record logs
                        $logs = "Record - " . $this->record->field->$logfield;
                        $this->log_model->table_logs($this->data['current_module']['module_label'], $this->table, $this->pfield, $this->record->pk, 'Delete', $logs);
                        
                        // successful
                        $data["class"] = "success";
                        $data["msg"] = $this->data['current_module']['module_label'] . " successfully deleted.";
                        $data["urlredirect"] = $this->controller_page . "/";
                        $this->load->view("header", $data);
                        $this->load->view("message");
                        $this->load->view("footer");
                    } else {
                        // error
                        $data["class"] = "danger";
                        $data["msg"] = "Error in deleting the " . $this->data['current_module']['module_label'] . "!";
                        $data["urlredirect"] = "";
                        $this->load->view("header", $data);
                        $this->load->view("message");
                        $this->load->view("footer");
                    }
                // } else {
                //     // error
                //     $data["class"] = "danger";
                //     $data["msg"] = "Data integrity constraints.";
                //     $data["urlredirect"] = "";
                //     $this->load->view("header", $data);
                //     $this->load->view("message");
                //     $this->load->view("footer");
                // }
            } else {
                // error
                $data["class"] = "danger";
                $data["msg"] = $this->data['current_module']['module_label'] . " record not found!";
                $data["urlredirect"] = "";
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            }
        } else {
            // error
            $data["class"] = "danger";
            $data["msg"] = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header", $data);
            $this->load->view("message");
            $this->load->view("footer");
        }
    }

    public function view($id)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);
        
        if ($this->roles['view']) {
            //change start
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->record->table = $this->table;            
            // set fields for the current table
            $this->record->setFields();         
            // extend fields - join tables 
            // extend fields - join tables   
            $this->record->fields[] = 'items.name';       
            $this->record->fields[] = 'items.description';       
            $this->record->fields[] = 'items.umsr';       
            $this->record->fields[] = 'ancillaries.officeName';       
            $this->record->fields[] = 'brands.brand';       

            // set joins
            $this->record->joins[]  = array('items',$this->table.'.itemID=items.itemID','left');
            $this->record->joins[]  = array('brands','items.brandID=brands.brandID','left');
            $this->record->joins[]  = array('ancillaries',$this->table.'.ancillaryID=ancillaries.ancillaryID','left');           
           
                              
            // set where
            $this->record->where[$this->table.'.'.$this->pfield] = $id;


            
            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;
            //change end

            //change start
            //change end
            
            $data['in_used'] = $this->_in_used($id);
            // record logs
            $pfield = $this->pfield;
            if ($this->config_model->getConfig('Log all record views') == '1') {
                $logs = "Record - " . $this->record->field->name;
                $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->field->$pfield, 'View', $logs);
            }
            
            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path . '/view');
            $this->load->view('footer');
        } else {
            // no access this page
            $data['class'] = "danger";
            $data['msg'] = "Sorry, you don't have access to this page!";
            $data['urlredirect'] = "";
            $this->load->view('header', $data);
            $this->load->view('message');
            $this->load->view('footer');
        }
    }

    // adjNo   date    branchID    itemID
    public function show()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;

        //=========================
        //change start
        $condition_fields = array(
            array('variable'=>'adjNo', 'field'=>$this->table.'.adjNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'date', 'field'=>$this->table.'.date', 'default_value'=>'', 'operator'=>'like_both'),
            // array('variable'=>'ancillaryID', 'field'=>$this->table.'.ancillaryID', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'name', 'field'=>'items.name', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'umsr', 'field'=>'items.umsr', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'adjType', 'field'=>$this->table.'.adjType', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('adjNo'=>'desc');
        //change end
        //=========================


        
        $controller = $this->uri->segment(1);
        //don't use segment(4) since you are not using pageType
        if ($this->uri->segment(3))
            $offset = $this->uri->segment(3);
        else
            $offset = 0;
        
        // source of filtering
        $filter_source = 0; // default/blank
        if ($this->input->post('filterflag') || $this->input->post('sortby')) {
            $filter_source = 1;
        } else {
            foreach ($condition_fields as $key) {
                if ($this->input->post($key['variable'])) {
                    $filter_source = 1; // form filters
                    break;
                }
            }
        }
        
        if (! $filter_source) {
            foreach ($condition_fields as $key) {
                if ($this->session->userdata($controller . '_' . $key['variable']) || $this->session->userdata($controller . '_sortby') || $this->session->userdata($controller . '_sortorder')) {
                    $filter_source = 2; // session
                    break;
                }
            }
        }
        
        switch ($filter_source) {
            case 1:
                foreach ($condition_fields as $key) {
                    $$key['variable'] = trim($this->input->post($key['variable']));
                }
                
                $sortby = trim($this->input->post('sortby'));
                $sortorder = trim($this->input->post('sortorder'));
                
                break;
            case 2:
                foreach ($condition_fields as $key) {
                    $$key['variable'] = $this->session->userdata($controller . '_' . $key['variable']);
                }
                
                $sortby = $this->session->userdata($controller . '_sortby');
                $sortorder = $this->session->userdata($controller . '_sortorder');
                break;
            default:
                foreach ($condition_fields as $key) {
                    $$key['variable'] = $key['default_value'];
                }
                $sortby = "";
                $sortorder = "";
        }
        
        if ($this->input->post('limit')) {
            if ($this->input->post('limit') == "All")
                $limit = "";
            else
                $limit = $this->input->post('limit');
        } else if ($this->session->userdata($controller . '_limit')) {
            $limit = $this->session->userdata($controller . '_limit');
        } else {
            $limit = 25; // default limit
        }
        
        // set session variables
        foreach ($condition_fields as $key) {
            $this->session->set_userdata($controller . '_' . $key['variable'], $$key['variable']);
        }

        
        $this->session->set_userdata($controller . '_sortby', $sortby);
        $this->session->set_userdata($controller . '_sortorder', $sortorder);
        $this->session->set_userdata($controller . '_limit', $limit);
        
        // assign data variables for views
        foreach ($condition_fields as $key) {
            //use value as variable name
            $data[$key['variable']] = $$key['variable'];
        }




        // First Query for Pagination config
        //===================================
        //change start
        
        
        // select
        $this->db->select($this->table.'.*');
        $this->db->select('items.name');       
        $this->db->select('items.description');       
        $this->db->select('items.umsr');       
        $this->db->select('ancillaries.officeName');       
        $this->db->select('brands.brand');   

        // from
        $this->db->from($this->table);
        
        // set joins
        $this->db->join('items',$this->table.'.itemID=items.itemID','left');
        $this->db->join('brands','items.brandID=brands.brandID','left');
        $this->db->join('ancillaries',$this->table.'.ancillaryID=ancillaries.ancillaryID','left');      
        
        // where
        if (!$this->session->userdata('current_user')->isAdmin) {
            $ancillaryID = $this->session->userdata('current_user')->ancillaryID;
            $this->db->where('ancillaries.ancillaryID', $ancillaryID);
        }
        $this->db->where($this->table.'.status !=', -100);
        //change end
        //===================================
        
            

             


        // set conditions here
        foreach ($condition_fields as $key) {
            $operators = explode('_', $key['operator']);
            $operator = $operators[0];
            // check if the operator is like
            if (count($operators) > 1) {
                // like operator
                if (trim($$key['variable']) != '' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable'], $operators[1]);
            } else {
                if (trim($$key['variable']) != '' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable']);
            }
        }
        
        // get
        $data['ttl_rows'] = $config['total_rows'] = $this->db->count_all_results();
        
        // set pagination
        $config['base_url'] = $this->controller_page . '/show/';
        $config['per_page'] = $limit;
        $this->pagination->initialize($config);





        // Second Query for retrieving data
        //===================================
        //change start
        
        
        // select
        $this->db->select($this->table.'.*');
        $this->db->select('items.name');       
        $this->db->select('items.description');       
        $this->db->select('items.umsr');       
        $this->db->select('ancillaries.officeName');       
        $this->db->select('brands.brand');   

        // from
        $this->db->from($this->table);
        
        // set joins
        $this->db->join('items',$this->table.'.itemID=items.itemID','left');
        $this->db->join('brands','items.brandID=brands.brandID','left');
        $this->db->join('ancillaries',$this->table.'.ancillaryID=ancillaries.ancillaryID','left');      
        
        // where
        if (!$this->session->userdata('current_user')->isAdmin) {
            $ancillaryID = $this->session->userdata('current_user')->ancillaryID;
            $this->db->where('ancillaries.ancillaryID', $ancillaryID);
        }
        $this->db->where($this->table.'.status !=', -100);
        //change end
        //===================================
        




        // set conditions here
        foreach ($condition_fields as $key) {
            $operators = explode('_', $key['operator']);
            $operator = $operators[0];
            // check if the operator is like
            if (count($operators) > 1) {
                // like operator
                if (trim($$key['variable']) != '' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable'], $operators[1]);
            } else {
                if (trim($$key['variable']) != '' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable']);
            }
        }
        
        if ($sortby && $sortorder) {
            $this->db->order_by($sortby, $sortorder);
            
            if (! empty($sorting_fields)) {
                foreach ($sorting_fields as $fld => $s_order) {
                    if ($fld != $sortby) {
                        $this->db->order_by($fld, $s_order);
                    }
                }
            }
        } else {
            $ctr = 1;
            if (! empty($sorting_fields)) {
                foreach ($sorting_fields as $fld => $s_order) {
                    if ($ctr == 1) {
                        $sortby = $fld;
                        $sortorder = $s_order;
                    }
                    $this->db->order_by($fld, $s_order);
                    
                    $ctr ++;
                }
            }
        }
        
        if ($limit) {
            if ($offset) {
                $this->db->limit($limit, $offset);
            } else {
                $this->db->limit($limit);
            }
        }
        
        // assigning variables
        $data['sortby'] = $sortby;
        $data['sortorder'] = $sortorder;
        $data['limit'] = $limit;
        $data['offset'] = $offset;
        
        // get
        $data['records'] = $this->db->get()->result();
        
        // load views
        $this->load->view('header', $data);
        $this->load->view($this->module_path . '/list');
        $this->load->view('footer');
    }

    public function printlist()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;

        //=========================
        //change start
        $condition_fields = array(
            array('variable'=>'adjNo', 'field'=>$this->table.'.adjNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'date', 'field'=>$this->table.'.date', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'branchID', 'field'=>$this->table.'.branchID', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'itemID', 'field'=>$this->table.'.itemID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'1', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('adjNo'=>'desc');
        //change end
        //=========================
        
        $controller = $this->uri->segment(1); // if print per page

        // uncomment if want to print with no offset
        // if ($this->uri->segment(3))
        //  $offset = $this->uri->segment(3);
        // else
        //  $offset = 0;
        
        foreach ($condition_fields as $key) {
            $$key['variable'] = $this->session->userdata($controller . '_' . $key['variable']);
        }
        
        
        $limit = $this->session->userdata($controller . '_limit');
        $offset = $this->session->userdata($controller . '_offset');
        $sortby = $this->session->userdata($controller . '_sortby');
        $sortorder = $this->session->userdata($controller . '_sortorder');
        


        //===================================
        //change start
        
        
        // select
        $this->db->select($this->table.'.*');
        $this->db->select('items.name');       
        $this->db->select('items.description');       
        $this->db->select('items.umsr');       
        $this->db->select('ancillaries.officeName');       
        $this->db->select('brands.brand');   

        // from
        $this->db->from($this->table);
        
        // set joins
        $this->db->join('items',$this->table.'.itemID=items.itemID','left');
        $this->db->join('brands','items.brandID=brands.brandID','left');
        $this->db->join('ancillaries',$this->table.'.ancillaryID=ancillaries.ancillaryID','left');      
        
        // where
        $this->db->where($this->table.'.status !=', -100);
        //change end
        //===================================


        
        // set conditions here
        foreach ($condition_fields as $key) {
            $operators = explode('_', $key['operator']);
            $operator = $operators[0];
            // check if the operator is like
            if (count($operators) > 1) {
                // like operator
                if (trim($$key['variable']) != '' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable'], $operators[1]);
            } else {
                if (trim($$key['variable']) != '' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable']);
            }
        }
        
        if ($sortby && $sortorder) {
            $this->db->order_by($sortby, $sortorder);
            
            if (! empty($sorting_fields)) {
                foreach ($sorting_fields as $fld => $s_order) {
                    if ($fld != $sortby) {
                        $this->db->order_by($fld, $s_order);
                    }
                }
            }
        } else {
            $ctr = 1;
            if (! empty($sorting_fields)) {
                foreach ($sorting_fields as $fld => $s_order) {
                    if ($ctr == 1) {
                        $sortby = $fld;
                        $sortorder = $s_order;
                    }
                    $this->db->order_by($fld, $s_order);
                    
                    $ctr ++;
                }
            }
        }
        
        if ($limit) {
            if ($offset) {
                $this->db->limit($limit, $offset);
            } else {
                $this->db->limit($limit);
            }
        }
        
        // assigning variables
        $data['sortby'] = $sortby;
        $data['sortorder'] = $sortorder;
        $data['limit'] = $limit;
        $data['offset'] = $offset;
        
        // get
        $data['records'] = $this->db->get()->result();
        
        $data['title'] = $this->module_label." List";
        
        // load views
        $this->load->view('header_print', $data);
        $this->load->view($this->module_path . '/printlist');
        $this->load->view('footer_print');
    }

    public function exportlist()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;

        //=========================
        //change start
        $condition_fields = array(
            array('variable'=>'adjNo', 'field'=>$this->table.'.adjNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'date', 'field'=>$this->table.'.date', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'branchID', 'field'=>$this->table.'.branchID', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'itemID', 'field'=>$this->table.'.itemID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'1', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('adjNo'=>'desc');
        //change end
        //=========================
        
        $controller = $this->uri->segment(1); // if print per page

        // uncomment if want to print with no offset
        // if ($this->uri->segment(3))
        //  $offset = $this->uri->segment(3);
        // else
        //  $offset = 0;
        
        foreach ($condition_fields as $key) {
            $$key['variable'] = $this->session->userdata($controller . '_' . $key['variable']);
        }
        
        
        $limit = $this->session->userdata($controller . '_limit');
        $offset = $this->session->userdata($controller . '_offset');
        $sortby = $this->session->userdata($controller . '_sortby');
        $sortorder = $this->session->userdata($controller . '_sortorder');
        


        //===================================
        //change start
        
        
        // select
        $this->db->select($this->table.'.*');
        $this->db->select('items.name');       
        $this->db->select('items.description');       
        $this->db->select('items.umsr');       
        $this->db->select('ancillaries.officeName');       
        $this->db->select('brands.brand');   

        // from
        $this->db->from($this->table);
        
        // set joins
        $this->db->join('items',$this->table.'.itemID=items.itemID','left');
        $this->db->join('brands','items.brandID=brands.brandID','left');
        $this->db->join('ancillaries',$this->table.'.ancillaryID=ancillaries.ancillaryID','left');      
        
        // where
        $this->db->where($this->table.'.status !=', -100);
        //change end
        //===================================


        
        // set conditions here
        foreach ($condition_fields as $key) {
            $operators = explode('_', $key['operator']);
            $operator = $operators[0];
            // check if the operator is like
            if (count($operators) > 1) {
                // like operator
                if (trim($$key['variable']) != '' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable'], $operators[1]);
            } else {
                if (trim($$key['variable']) != '' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable']);
            }
        }
        
        if ($sortby && $sortorder) {
            $this->db->order_by($sortby, $sortorder);
            
            if (! empty($sorting_fields)) {
                foreach ($sorting_fields as $fld => $s_order) {
                    if ($fld != $sortby) {
                        $this->db->order_by($fld, $s_order);
                    }
                }
            }
        } else {
            $ctr = 1;
            if (! empty($sorting_fields)) {
                foreach ($sorting_fields as $fld => $s_order) {
                    if ($ctr == 1) {
                        $sortby = $fld;
                        $sortorder = $s_order;
                    }
                    $this->db->order_by($fld, $s_order);
                    
                    $ctr ++;
                }
            }
        }
        
        if ($limit) {
            if ($offset) {
                $this->db->limit($limit, $offset);
            } else {
                $this->db->limit($limit);
            }
        }
        
        // assigning variables
        $data['sortby'] = $sortby;
        $data['sortorder'] = $sortorder;
        $data['limit'] = $limit;
        $data['offset'] = $offset;
        
        // get
        $records = $this->db->get()->result();
        $title = $this->module_label." List";
        $companyName = $this->config_model->getConfig('Company');
        $address = $this->config_model->getConfig('Address');
        
        // XML Blurb
        $data = "<?xml version='1.0'?>
            
            <?mso-application progid='Excel.Sheet'?>
            
            <Workbook xmlns='urn:schemas-microsoft-com:office:spreadsheet' xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns:ss='urn:schemas-microsoft-com:office:spreadsheet' xmlns:html='http://www.w3.org/TR/REC-html40'>
            <Styles>
            <Style ss:ID='s20'>
                <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
              <Font ss:Bold='1' ss:Size='14'/>
            </Style>
            
            <Style ss:ID='s23'>
              <Font ss:Bold='1'/>
            <Borders>
                    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
                   </Borders>
            </Style>
            
            <Style ss:ID='s24'>
                <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
                <Font ss:Bold='1'/>
            </Style>
            
            <Style ss:ID='s24A'>
                <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
            </Style>
            
            <Style ss:ID='s24B'>
                <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
               <Borders>
                    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
                   </Borders>
            </Style>
            
            <Style ss:ID='s25'>
                <Alignment ss:Horizontal='Right' ss:Vertical='Bottom'/>
                <NumberFormat ss:Format='#,##0.00_ ;[Red]\-#,##0.00\ '/>
                <Font ss:Bold='1'/>
            </Style>
            <Style ss:ID='s26'>
                <Alignment ss:Horizontal='Right' ss:Vertical='Bottom'/>
                <NumberFormat ss:Format='#,##0.00_ ;[Red]\-#,##0.00\ '/>
               <Borders>
                    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
                   </Borders>
            </Style>
            <Style ss:ID='s27'>
                  <Borders>
                    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
                   </Borders>
            </Style>
            </Styles>
            
            <Worksheet ss:Name='" . $title . "'>
                
            <Table>
            <Column ss:Index='1' ss:AutoFitWidth=\"1\" ss:Width='25.00'/>
            <Column ss:Index='2' ss:AutoFitWidth=\"1\" ss:Width='120.00'/>
            <Column ss:Index='3' ss:AutoFitWidth=\"1\" ss:Width='120.00'/>
            <Column ss:Index='4' ss:AutoFitWidth=\"1\" ss:Width='120.00'/>
            <Column ss:Index='5' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
            <Column ss:Index='6' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
                ";
        
        // header
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='9'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s20'>";
        $data .= "<Cell ss:MergeAcross='9'><Data ss:Type='String'>" . $companyName . "</Data></Cell>";
        $data .= "</Row>";
        $data .= "<Row ss:StyleID='s24A'>";
        $data .= "<Cell ss:MergeAcross='9'><Data ss:Type='String'>" . $address . "</Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='9'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='9'><Data ss:Type='String'>" . strtoupper($title) . "</Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='9'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
        
        $fields[] = "  ";
        $fields[] = "ADJUSTMENT NO.";
        $fields[] = "DATE";
        $fields[] = "OFFICE / DEPT.";
        $fields[] = "BRAND";
        $fields[] = "ITEM";
        $fields[] = "UNIT";
        $fields[] = "TYPE";
        $fields[] = "QTY";
        $fields[] = "STATUS";

        
        $data .= "<Row ss:StyleID='s24'>";
        // Field Name Data
        foreach ($fields as $fld) {
            $data .= "<Cell ss:StyleID='s23'><Data ss:Type='String'>$fld</Data></Cell>";
        }
        $data .= "</Row>";
        
        if (count($records)) {
            $ctr = 1;
            foreach ($records as $row) {
                $data .= "<Row>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $ctr . ".</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->adjNo . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . date('F d Y', strtotime($row->dateInserted)) . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->officeName . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->brand . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->name . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->umsr . "</Data></Cell>";
                if ($row->adjType == 'DR') {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Debit</Data></Cell>";
                } else if ($row->adjType == 'CR') {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Credit</Data></Cell>";
                }

                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->qty . "</Data></Cell>";

                if ($row->status == 1) {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Pending</Data></Cell>"; 
                } else if ($row->status == 2) {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Confirmed</Data></Cell>"; 
                } else if ($row->status == 0) {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Cancelled</Data></Cell>"; 
                }

                
                $data .= "</Row>";
                
                $ctr ++;
            }
        }
        $data .= "</Table></Worksheet>";
        $data .= "</Workbook>";
        
        // Final XML Blurb
        $filename = $this->data['controller_name'].'_list';
        
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$filename.xls;");
        header("Content-Type: application/ms-excel");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        echo $data;
    }

    // Conditions and fields changes
    // public function check_duplicate()
    // {
    //     //change start
    //     $this->db->where('groupName', trim($this->input->post('groupName')));
    //     //change end
        
    //     if ($this->db->count_all_results($this->table))
    //         echo "1"; // duplicate
    //     else
    //         echo "0";
    // }




    //============================================
    // Module Specific Functions
    //============================================
    private function _generateID()
    {
        $idSeries   = $this->config_model->getConfig('Job Post Series');
        $idLength   = $this->config_model->getConfig('Job Post Series Length');
    
        $id  = str_pad($idSeries, $idLength, "0", STR_PAD_LEFT);
        return "JP".date('y').$id;
    }   
    
    public function getPositionDuties()
    {
        $jobPositionID = trim($this->input->post('jobPositionID'));
        
        if ($jobPositionID) {
            $this->db->where('jobPositionID', $jobPositionID);
        }
        $this->db->order_by('duty','asc');
        $records = $this->db->get('position_duties');
        echo $this->frameworkhelper->get_json_data($records, 'jobPostID', 'duty');
    }   
    
    public function display_session()
    {               
        echo var_dump($_SESSION);
    }


}