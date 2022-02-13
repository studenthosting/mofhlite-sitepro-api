<?php 
public function getHostingAccountInfo(string $account_key)
	{
		$isLogged = $this->isLogged();
		if($isLogged['status'] == 'failed')
		{
			return $isLogged;
		}
		else
		{
			$account_key = $this->validateData($account_key, 'string')['data'];
			$client_key = $this->getLoggedClientInfo('client_key')['data'];
			$sql = $this->db->query("SELECT * FROM `".$this->table_prefix."hosting` WHERE `hosting_key` = '".$account_key."' AND `hosting_for` ='".$client_key."'");
			if($sql->num_rows > 0)
			{
				$data = $sql->fetch_assoc();
				$status = $data['hosting_status'];
				if($status == 0)
				{
					$status = 'proccessing';
				}
				elseif($status == 1)
				{
					$status = 'active';
				}
				elseif($status == 2)
				{
					$status = 'deactivated';
				}
				elseif($status == 3)
				{
					$status = 'suspnded';
				}
				elseif($status == 4)
				{
					$status = 'deactivating';
				}
				elseif($status == 5)
				{
					$status = 'reactivating';
				}
				return [
					'status' => 'success',
					'data' => [
						'general' => [
							'username' => $data['hosting_username'],
							'password' => $data['hosting_password'],
							'cpanel_uri' => $this->config['mofh']['cpanel_uri'],
							'created_on' => date('d F Y', $data['hosting_date']),
							'client_domain' => $data['hosting_domain'],
							'main_domain' => strtolower(str_replace('cpanel', $data['hosting_key'], $this->config['mofh']['cpanel_uri'])),
							'status' => $status,
							'ns_1' => $this->config['mofh']['ns_1'],
							'ns_2' => $this->config['mofh']['ns_2']
						],
						'ftp' => [
							'username' => $data['hosting_username'],
							'password' => $data['hosting_password'],
							'host_uri' => str_replace('cpanel', 'ftp', $this->config['mofh']['cpanel_uri']),
							'port' => '21'
						],
						'mysql' => [
							'username' => $data['hosting_username'],
							'password' => $data['hosting_password'],
							'host_uri' => str_replace('cpanel', $data['hosting_sql'], $this->config['mofh']['cpanel_uri']),
							'port' => '3306'
						],
						'link' => [
							'file_manager' => 'https://filemanager.ai/new/#/c/ftpupload.net/'.$data['hosting_username'].'/'.base64_encode(
								json_encode(['t' => 'ftp', 'c' => ['v' => 1, 'p' => $data['hosting_password']]]))
						]
					]
				];
			}
			else
			{
				return [
			       'status' => 'failed',
			       'data' => "No web hosting account found with this key!"
			     ];
			}
		}
	}
	?>