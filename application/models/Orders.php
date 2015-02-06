<?php

/**
 * Data access wrapper for "orders" table.
 *
 * @author jim
 */
class Orders extends MY_Model {

    // constructor
    function __construct() {
        parent::__construct('orders', 'num');
    }
    
    // add an item to an order
    function add_item($num, $code) {
        $CI = &get_instance();
        $CI->load->model('orderitems');
        $CI->load->model('orders');
        if ($CI->orderitems->exists($num, $code))
        {
            $record = $CI->orderitems->get($num, $code);
            $record->quantity++;
            $CI->orderitems->update($record);
        }
        else
        {
            $record = $CI->orderitems->create();
            $record->order = $num;
            $record->item = $code;
            $record->quantity = 1;
            $CI->orderitems->add($record);
        }
        
        $orderRecord = $CI->orders->get($num);
        $orderRecord->total = $this->total($num);
        $CI->orders->update($orderRecord);
    }

    // calculate the total for an order
    function total($num) {
        $CI = &get_instance();
        $CI->load->model('orderitems');
        
        $items = $CI->orderitems->some('order', $num);
        
        $result = 0;
        foreach ($items as $item)
        {
            $menuitem = $this->menu->get($item->item);
            $result += $item->quantity * $menuitem->price;
        }
        
        return $result;
    }

    // retrieve the details for an order
    function details($num) {
        
    }

    // cancel an order
    function flush($num) {
        
    }

    // validate an order
    // it must have at least one item from each category
    function validate($num) {
        return false;
    }
}
