<?php

if(isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] != '/'){

	$controller_option_list = explode('/', $_SERVER['PATH_INFO']);

	foreach ($controller_option_list as $key => $value) {
		if($value != '')
			$controller_option[] = $value;
	}

	if(file_exists(DOCUMENT_ROOT.BASEDIR.APPLICATION.'/controllers/'.$controller_option[0].'.php'))
		$obj = new $controller_option[0];
	else
		{
			include(APPLICATION.'/config/route.php');			
			if($config['url_404'] === '')
				{
					include(SYSTEM.'/404.php');
					exit;
				}
			else
			{
				$custome_url = explode('/', $config['url_404'] );

				if(file_exists(DOCUMENT_ROOT.BASEDIR.APPLICATION.'/controllers/'.$custome_url[0].'.php'))
					redirect($config['url_404']);
				else
					{
					include(SYSTEM.'/404.php');
					exit;
				}
			}
			
		}

	array_shift($controller_option);

	$obj_class 	= get_class($obj);		
	$obj_meth 	= get_class_methods($obj);

	if(count($controller_option) < 1 && in_array('index', $obj_meth))
		$obj->index();
	else
	{		

		$method = $controller_option[0];
		array_shift($controller_option);

		$prm = $controller_option;		

		if(in_array($method, $obj_meth))
			$obj->$method(	@$prm[0],@$prm[1],@$prm[2],@$prm[3],@$prm[4],@$prm[5],
						@$prm[6],@$prm[7],@$prm[8],@$prm[9],@$prm[10],@$prm[11],@$prm[12],
						@$prm[13],@$prm[14],@$prm[15],@$prm[16],@$prm[17],@$prm[18],@$prm[19]
					);
		else
		{
			include(APPLICATION.'/config/route.php');			
			if($config['url_404'] === '')
				{
					include(SYSTEM.'/404.php');
					exit;
				}
			else
			{
				$custome_url = explode('/', $config['url_404'] );

				if(file_exists(DOCUMENT_ROOT.BASEDIR.APPLICATION.'/controllers/'.$custome_url[0].'.php'))
					redirect($config['url_404']);
				else
					{
						include(SYSTEM.'/404.php');
						exit;
					}
			}
			
		}
	}	

}
else{
	

	if(file_exists(DOCUMENT_ROOT.BASEDIR.APPLICATION.'/controllers/'.$config['default_controller'].'.php'))
		{			
			$obj = new $config['default_controller'];				
			$obj_meth 	= get_class_methods($obj);
		}
	else
		{
			include(SYSTEM.'/404.php');
			exit;
		}
	if(in_array('index', $obj_meth))
		$obj->index();
	
}

?>