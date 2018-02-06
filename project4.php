<pre>
&lt;html&gt;
&lt;head&gt;&lt;title&gt;Shopping Site&lt;/title&gt;&lt;/head&gt;
&lt;body&gt;
&lt;?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors','On');
$xmlListstr = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/CategoryTree?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&amp;visitorUserAgent&amp;visitorIPAddress&amp;trackingId=7000610&amp;categoryId=72&amp;showAllDescendants=true');
$xmlList = new SimpleXMLElement($xmlListstr);
$total_item=0;
if(isset($_GET['buy'])) 
{
	$id_item=$_GET['buy'];
	$src_item='http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&amp;visitorUserAgent&amp;visitorIPAddress&amp;trackingId=7000610&amp;productId='.$id_item;
	$str_item=file_get_contents($src_item);
	$xml_item=new SimpleXMLElement($str_item);
	$img_item=(string)$xml_item-&gt;categories-&gt;category-&gt;items-&gt;product-&gt;images-&gt;image[0]-&gt;sourceURL;
	$name_item=(string)$xml_item-&gt;categories-&gt;category-&gt;items-&gt;product-&gt;name;
	$price_item=(string)$xml_item-&gt;categories-&gt;category-&gt;items-&gt;product-&gt;minPrice;
	$offer_item=(string)$xml_item-&gt;categories-&gt;category-&gt;items-&gt;product-&gt;productOffersURL;
	$array_items=array();
	array_push($array_items,$id_item);
	array_push($array_items,$img_item);
	array_push($array_items,$name_item);
	array_push($array_items,$price_item);
	array_push($array_items,$offer_item);
	if(!in_array($id_item,$_SESSION['itemId']))
	{
		$_SESSION['itemId'][$id_item]=$id_item;
		$_SESSION['basket'][$id_item]=$array_items;
		
	}
}
if(!isset($_SESSION['basket']) || $_SESSION['itemId']==null)
{
  $_SESSION['basket']=array();
  $_SESSION['itemId']=array();
}
elseif (isset($_GET['remove'])) 
{
  session_unset();
  $_SESSION['basket']=array();
  $_SESSION['itemId']=array();
}
elseif(isset($_GET['delete']))
{
	$item_delete=$_GET['delete'];
	unset($_SESSION['basket'][$item_delete]);
	unset($_SESSION['itemId'][$item_delete]);
}
?&gt;
&lt;p&gt;Shopping Basket:&lt;/p&gt;
&lt;?php
if(!empty($_SESSION['basket']))
{   echo &quot;&lt;table border=\&quot;1\&quot;&gt;&quot;;
	foreach($_SESSION['basket'] as $product)
	{
		if($product!='')
		{   
			echo &quot;&lt;tr&gt;&quot;;
			$delete_link='buy.php?delete='.$product[0];
			echo &quot;&lt;td&gt;&lt;a href='&quot;.$product[4].&quot;'&gt;&lt;img src=\&quot;&quot;.$product[1].&quot;\&quot;&gt;&lt;/img&gt;&lt;/a&gt;&lt;/td&gt;&quot;;
			echo &quot;&lt;td&gt;&quot;.$product[2].&quot;&lt;/td&gt;&quot;;
			echo &quot;&lt;td&gt;$&quot;.$product[3].&quot;&lt;/td&gt;&quot;;
			$total_item=$total_item+$product[3];
			echo &quot;&lt;td&gt;&lt;a href='&quot;.$delete_link.&quot;'&gt;delete&lt;/td&gt;&lt;/tr&gt;&quot;;
		}
	}
	echo &quot;&lt;/table&gt;&quot;;
}
echo &quot;Total:$&quot; .$total_item;
?&gt;
&lt;form action=&quot;buy.php&quot; method=&quot;GET&quot;&gt;
&lt;input type=&quot;hidden&quot; name=&quot;remove&quot; value=&quot;1&quot;&gt;
    &lt;input type=&quot;submit&quot; value=&quot;Clear Cart&quot;&gt;
&lt;/form&gt;
&lt;form action=&quot;buy.php&quot; method=&quot;GET&quot;&gt;
&lt;fieldset&gt;
&lt;legend style=&quot;width:500px,height=500px&quot;&gt;Find product&lt;/legend&gt;&lt;label&gt;Category:&lt;select name=&quot;category&quot;&gt;&lt;/label&gt;     
&lt;?php
error_reporting(E_ALL);
ini_set('display_errors','On');
$xmlstr = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/CategoryTree?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&amp;visitorUserAgent&amp;visitorIPAddress&amp;trackingId=7000610&amp;categoryId=72&amp;showAllDescendants=true');
$xml = new SimpleXMLElement($xmlstr);
echo &quot;&lt;optgroup label=&quot;.$xml-&gt;category-&gt;name.&quot;&gt;&quot;;
echo &quot;&lt;option value=&quot;.$xml-&gt;category-&gt;categories-&gt;category['id'].&quot;&gt;&quot;.$xml-&gt;category-&gt;categories-&gt;category-&gt;name.&quot;&lt;/option&gt;&lt;/optgroup&gt;&quot;;
foreach ($xml-&gt;category-&gt;categories-&gt;{'category'} as $availabe_categories)
	{  
		echo &quot;&lt;optgroup label=&quot;.$availabe_categories-&gt;name.&quot;&gt;&quot;;
		foreach ($availabe_categories-&gt;categories-&gt;{'category'} as $availabe_subcategories)
		{
		$subcat_id=$availabe_subcategories['id'];
		echo &quot;&lt;option value=&quot;.$subcat_id.&quot;&gt;&quot;.$availabe_subcategories-&gt;name.&quot;&lt;/option&gt;&quot;;
		}
		echo &quot;&lt;/optgroup&gt;&quot;;
	}
?&gt;
&lt;/select&gt;
&lt;label&gt;Search Keywords:&lt;input type=&quot;text&quot; name=&quot;keyword&quot;/&gt;&lt;/label&gt;
&lt;input type=&quot;submit&quot;  value=&quot;Search&quot; /&gt;	   
&lt;/fieldset&gt;
&lt;/form&gt;
&lt;table&gt;
&lt;?php
	if(isset($_GET['keyword'])&amp;&amp;($_GET['category']))
	{
		$id_category=$_GET['category'];
		$id_keyword=$_GET['keyword'];
		$url_keyword=str_replace(' ','+',$id_keyword);
		$str_xml = file_get_contents('http://sandbox.api.shopping.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&amp;trackingId=7000610&amp;categoryId='.$id_category.'&amp;keyword='.$url_keyword.&quot;&amp;numItems=20&quot;);
		$xml_detail = new SimpleXMLElement($str_xml);
		foreach ($xml_detail-&gt;categories-&gt;category-&gt;{'items'} as $availabe_items){	
			foreach($availabe_items-&gt;product as $available_categories)
			{
				$offer_link='buy.php?buy='.$available_categories['id'];
				echo &quot;&lt;tr&gt;&quot;;
				echo &quot;&lt;td&gt;&lt;a href='&quot;.$offer_link.&quot;'&gt;&lt;img src=\&quot;&quot;.$available_categories-&gt;images-&gt;image-&gt;sourceURL.&quot;\&quot;&gt;&lt;/a&gt;&lt;/td&gt;&quot;;
				echo &quot;&lt;td&gt;&quot;.$available_categories-&gt;name.&quot;&lt;/td&gt;&quot;; 
				echo &quot;&lt;td&gt;&quot;.$available_categories-&gt;minPrice.&quot;&lt;/td&gt;&quot;;
				echo &quot;&lt;td&gt;&quot;.$available_categories-&gt;fullDescription.&quot;&lt;/td&gt;&lt;/tr&gt;&quot;;
			}
		}
	}
?&gt;
&lt;/table&gt;
&lt;/body&gt;
&lt;/html&gt;</pre>
