<?php

class AdminController extends BaseController {
	
		 public function __construct()
    {
        $this->beforeFilter('is_admin');
    }
	public function getName()
	{
		 $username = Input::get('username'); 
		$account = Account::where('username','LIKE', $username.'%')
		->groupBy('username')
		->orderBy('username', 'ASC')->get();
		
		return Response::json($account);
	}
	
		public function getAccounts()
	{
		
		return View::make('accounts/accounts');
	}
			public function getUsers()
	{
		
		return View::make('accounts/users');
	}
	
			public function getUsername()
	{
		
		$username = Input::get('username');
		$user = User::with('account')
		->where('username','LIKE', $username.'%')
		->orderBy('account_id', 'ASC')
		->orderBy('username', 'ASC')->get();
		return Response::json($user);
	}
	
	public function getAccount($id)
	{
		$account= Account::where('id','=',$id)->with('user')->first();
		//$users = User::where('account_id','=',$id)->get();
		if (isset($account->access) && $account->access !=''){
			$access = str_split($account->access);
			if ($access[0]) $access_array[0] = "Motorcycle"; else $access_array[0] ='';
			if ($access[1]) $access_array[1] = "ATV"; else $access_array[1] ='';
			if ($access[2]) $access_array[2] = "Snowmobile"; else $access_array[2] ='';
			if ($access[3]) $access_array[3] = "Watercraft"; else $access_array[3] ='';
			 return View::make('accounts/accounts')->with('account', $account)->with('access_array',$access_array);
		} else {
		 return View::make('accounts/accounts')->with('account', $account);
		}
		
	}
	
	
	
	
	public function postCreate()
	{
		 $inputs = Input::all();
		 $access_array =Input::get('access_array');
			if(in_array('Motorcycle',$access_array)) $access=1; else $access=0;
			if(in_array('ATV',$access_array)) $access.=1; else $access.=0;
			if(in_array('Snowmobile',$access_array)) $access.=1; else $access.=0;
			if(in_array('Watercraft',$access_array)) $access.=1; else $access.=0;
		 $rules = array ();
		 $validator = Validator::make($inputs, $rules);
		if ($validator->passes()){
		  $account=Account::where('id','=',  Input::get('id'))->first();
		 if (is_null($account))
		 {
		// Create a new Role
		 	  $account = new Account;
		          $account->username = 	Input::get('username');
		 	  $account->firm_name = Input::get('firmname');
		 	  $account->address = 	Input::get('address1');
			  $account->address1 = 	Input::get('address2');
		 	  $account->city = 	Input::get('city');
		 	  $account->prov = 	Input::get('prov');
			  $account->postal =	Input::get('postal');
		 	  $account->phone = 	Input::get('phone');
		 	  $account->fax = 	Input::get('fax');
			  $account->expiry = 	Input::get('expiry');
		 	  $account->email = 	Input::get('email');
		 	  $account->access = 	$access;
			  $account->m_or_d = 	Input::get('m_or_d');
		 	  $account->save();
		 } else {
		          $account->username = 	Input::get('username');
		 	  $account->firm_name = Input::get('firmname');
		 	  $account->address = 	Input::get('address1');
			  $account->address1 = 	Input::get('address2');
		 	  $account->city = 	Input::get('city');
		 	  $account->prov = 	Input::get('prov');
			  $account->postal =	Input::get('postal');
		 	  $account->phone = 	Input::get('phone');
		 	  $account->fax = 	Input::get('fax');
			  $account->expiry = 	Input::get('expiry');
		 	  $account->email = 	Input::get('email');
		 	  $account->access = 	$access;
			  $account->m_or_d = 	Input::get('m_or_d');
		 	  $account->save();
				  
		 }
		
		 
		 return Redirect::to('/admin/account/'.$account->id)->with('success_message',$account->firm_name.' saved');
		}
		else
		{
		  return Redirect::to('/admin/accounts')->with('error_message', 'Validation error')->withErrors($validator)->withInput();
		}	
	}
		
		public function getUser($account_id,$id=null)
		{
		$account = Account::where('id','=',$account_id)->first();
		$roles= Toddish\Verify\Models\Role::lists('name', 'id');
		if (isset($id)){
		$user= Toddish\Verify\Models\User::where('id','=',$id)->where('account_id','=',$account_id)->first();
		if (is_null($user)){
			return Redirect::to('/admin/account/'.$account->id)->with('error_message','User ID not found');
		} else {
		return View::make('accounts/users')->with('roles',$roles)->with('account',$account)->with('user',$user);
		}
		} else {
		  return View::make('accounts/users')->with('roles',$roles)->with('account',$account);
		}
		}
		
		
		public function postUser()
		{
		$inputs = Input::all();
		 $rules = array ();
		 $validator = Validator::make($inputs, $rules);
		if ($validator->passes()){
		  $user=Toddish\Verify\Models\User::where('id','=',  Input::get('id'))->first();
		 if (is_null($user)){	
			$user= new Toddish\Verify\Models\User;
			$user->username = Input::get('username');
			$user->email = Input::get('email');
			$user->password = Input::get('password');
			$user->account_id = Input::get('account_id');
			$user->save();	
		} else {
			$user->email = Input::get('email');
			$pword = Input::get('password');
			if (!empty($pword))
			$user->password = Input::get('password');
			$user->save();
		}
		$role = Input::get('role');
		if (!empty($role))
		$user->roles()->sync(array($role));
		return Redirect::to('/admin/user/'.$user->account_id.'/'.$user->id)->with('success_message',$user->email.'saved');
		}
		else
		{
		  return Redirect::to('/admin/account/'.$user->account_id)->with('error_message', 'Validation error')->withErrors($validator)->withInput();
		}	
}
		public function missingMethod($parameters = array())
		{
			return View::make('error');
		}


}