<?php
class ControllerCheckoutSuccess extends Controller {
	public function index() {
		$this->load->language('checkout/success');

		if (isset($this->session->data['order_id'])) {
		    $products = $this->cart->getProducts();
		    $jsonProducts = [];
		    $productsCount = count($products);
		    foreach ($products as $product) {
				$currentArary = array();
				$currentArary['name'] = $product['name'];
				$currentArary['quantity'] = $product['quantity'];
				$currentArary['xPos'] = $product['xPos'];
				$currentArary['yPos'] = $product['yPos'];
				$currentArary['unitID'] = $product['unit_id'];/// check this
				$currentArary['bentCount'] = $product['bent_count'];

		    	// productName as a helper, 
		    	$jsonProducts[] = $currentArary;
		    }
			print_r($products);
			//$products = array();// quantity to be added
			$productsCount = count($products);
			$order = array(
				'OrderID'       => $this->session->data['order_id'],
				'ProductsCount' => $productsCount,
				'Products'      => $jsonProducts,
				'OrderStatus'   => 'waiting'
			);
			$json_data = json_encode($order);// path need to be changed
			$result = file_put_contents('data.json', $json_data);			
			print_r('Result: ');
			print_r($result);
			$this->cart->clear();

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_basket'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_checkout'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('checkout/success')
		);

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', true), $this->url->link('account/order', '', true), $this->url->link('account/download', '', true), $this->url->link('information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		try {
			$address="192.168.1.37";//127.0.0.1
			$port="11111";
			$msg="Hello server";
			
			$sock=socket_create(AF_INET,SOCK_STREAM,0) or die("Cannot create a socket");
			socket_connect($sock,$address,$port) or die("Could not connect to the socket");
			socket_write($sock,$json_data);
			
			$read=socket_read($sock,1024);
			echo $read; // if a certain message comes, it is okay, otherwise store in the database and the events what happens and how to be handled
			socket_close($sock);
			$this->response->setOutput($this->load->view('common/success', $data));

				
		} catch (Exception $e) {
			print_r($e.Message);
			//throw $th;
		}
	}
}