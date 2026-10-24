<?php
	include('connect.php');

	function getSaleCustomer($customer_id){
		$conn = $GLOBALS['conn_pos'];
		
		// Prepared statement for mysqli
		$stmt = $conn->prepare("SELECT * FROM customers WHERE Customer_ID = ?");
		$stmt->bind_param("i", $customer_id);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$customer = $result->fetch_assoc();
		
		$stmt->close();
		return $customer;
	}

	function getOrderItems($sales_id)
	{
		$conn = $GLOBALS['conn_pos'];
		
		$stmt = $conn->prepare("SELECT * FROM sales_item WHERE Sales_ID = ?");
		$stmt->bind_param("i", $sales_id);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$rows = $result->fetch_all(MYSQLI_ASSOC);
		
		$stmt->close();
		return $rows;
	}

	function getSale($sale_id)
	{
		$conn = $GLOBALS['conn_pos'];
		
		$stmt = $conn->prepare("SELECT * FROM sales WHERE Sales_ID = ?");
		$stmt->bind_param("i", $sale_id);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$sale = $result->fetch_assoc();
		$stmt->close();

		if (!$sale) {
			return null;
		}

		// Get customer data using Customer_ID
		$customers_data = getSaleCustomer($sale['Customer_ID']);

		// Get order items data
		$items = getOrderItems($sale['Sales_ID']);
		$items_data = [];

		$inv_conn = $GLOBALS['conn'];
		foreach($items as $item){
			$pid = $item['Product_ID'];

			$stmtInv = $inv_conn->prepare("SELECT ProductName FROM product WHERE Product_ID = ?");
			$stmtInv->bind_param("i", $pid);
			$stmtInv->execute();
			
			$resInv = $stmtInv->get_result();
			$product = $resInv->fetch_assoc();
			$stmtInv->close();

			$item_id = $item['SalesItem_ID'];
			$items_data[$item_id] = $item;
			$items_data[$item_id]['ProductName'] = $product['ProductName'] ?? 'Unknown Product';
		}

		return [
			'sales' => $sale,
			'items' => $items_data,
			'customer' => $customers_data
		];
	}
?>

    



