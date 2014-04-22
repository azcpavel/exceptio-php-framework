#Exceptio PHP Framework


#Develop By
#Ahsan Zahid Chowdhury
#azc.pavel@gmail.com
#+880-1534-302690


This is a product of Exception Solutions

Most of the methods are same as Codeigniter FW

Main Controller is EX_Controller
List of property

load-----
		|-model('model name') //of applicaion models folder
		|-library('name', config array) //of system libraries folder
		|-helper('helper name') //of applicaion helpers folder

input----
		|-post('name',XSS_clr = FALSE)
		|-get('name',XSS_clr = FALSE)
		|-request('name',XSS_clr = FALSE)
		|-files('name',XSS_clr = FALSE)

view-----
		|-page('view page name') //of applicaion views folder
		|-library('view library name') //of applicaion libraries folder	
		|-input class
		|-session class
		|-server class
		|-global class

file-----
		|-config_upload(array config)
		|-do_upload('post file name')
		|-upload_data()
		|-upload_error()
		|-mk_download('path')
		|-do_download('output file name' , 'download data')

session--
		|-set_userdata('session name', 'session data')
		|-userdata('session name')
		|-unset_userdata('session name')
		|-session_destroy()

server---
		|-get_all()
		|-get('name')

globals--
		|-get_all()
		|-get('name')

validate-
		|-set_rules($post,$field_name,$rules,$match_val = '') 
		//$rules 'required'|'match'|'email'|'int'|'url'|'trim'|'tag_clr'
		|-run()
		|-error()





Main Model is EX_Model
List of property

load-----
		|-database('name' = default)

db-------
		|-query()
		|-get($table = 0, $offset = 0, $limit = 0)
		|-get_where($table = 0, $where = 1, $offset =0, $limit = 0)
		|-select($select = '*')
		|-select_max($MAX, $as = 0)
		|-select_min($MIN, $as = 0)	
		|-select_avg($AVG, $as = 0)
		|-from($table)
		|-join($table, $join, $pos = 0)
		|-where($where = 1)
		|-order_by($order_by = 0, $order = 0)
		|-limit($offset = 0, $limit = 0)
		|-num_rows()
		|-row_array()
		|-result_array()
		|-row()
		|-result()
		|-insert($table, $values = 1)
		|-update($table, $values = '')
		|-delete($table, $where = 1)
		|-affected_rows()
		|-insert_id()
		

input----
		|-post('name',XSS_clr = FALSE)
		|-get('name',XSS_clr = FALSE)
		|-request('name',XSS_clr = FALSE)
		|-files('name',XSS_clr = FALSE)

session--
		|-set_userdata('session name', 'session data')
		|-userdata('session name')
		|-unset_userdata('session name')
		|-session_destroy()

server---
		|-get_all()
		|-get('name')

globals--
		|-get_all()
		|-get('name')




Global Functions

base_url()
site_url($address = '')
redirect($address = '')
form_mpt($address)
form_spt($address)
uri_segment($no)
&get_controller_instance() get main controller instance
&get_model_instance() get main model instance



Config folder
application/config

Controller folder
application/controllers

Model folder
application/models

View folder
application/views



System libraries
imgresize----
			|-resizeImage(width, height, maintain ratio 0 or 1)
			|-saveImage('images/cars/large/output.jpg', 100)



Must change the root .htaccess file