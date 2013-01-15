<?php
class TemplateConfig{
	
	static $LAYOUT_ARRAY =  array('canvas','header-wrapper','header','body-wrapper','body-container','container','footer-wrapper','footer');
	static $BODY_ARRAY = array('left-body-block','main-body-block','right-body-block');
	static $WRAPPER_ARRAY = array('header-wrapper','body-wrapper','footer-wrapper');
	static $BODY_PATTERN_ARRAY = array('header','body-container','footer');
	static $CLASS_ARRAY = array('blocktitle','container','blockcontent','input','button','input-class','text-class','container','dropdown','dir');
	static $ATTRIBUTE_ARRAY = array('class','type','rel','src');
	
	static $HEADER_ID = 1;
    static $BODY_ID = 3;
    static $FOOTER_ID = 2;
	
    static $FONT = array('Inherited','Arial','Times New Roman','Cabin Sketch','Goudy Bookletter 1911','Pacifico','Courier','Trebuchet','Copse','Helvetica','Raleway','Georgia','Verdana','Droid Sans','Josefin Slab','Wire One','Tahoma','Lucida','Droid Serif','Oswald','Yanone Kaffeesatz');
	static $FONT_WEIGHT = array('Normal','Bold');
	static $FONT_STYLE = array('Normal','Italic');
	static $TEXT_TRANSFORM = array('None','UPPERCACE','lowercase','Capitalize');
	static $TEXT_DECORATION = array('None','Underline','Overline','Strikethrough');
	static $TEXT_ALIGNMENT = array('Left Align','Right Align','Center Align','Justified (Full)');
	static $BORDER = array('None','Bottom','Left','Right','Top','Top & Bottom','Left & Right','Full');
	static $BORDER_STYLE = array('None','Solid','Dotted','Dashed');
	static $BACKGROUND_REPEAT = array('Don\'t Repeat','Repeat Horizontal','Repeat Vertical');
	static $FLOAT = array('Left','Right');
	static $ALIGN = array('Center','Left','Right');
	
	static $NODE_ATTRIBUTES = array('heading','order-by','display-count','content','location','visibility');
    static $LOGO_ATTRIBUTES = array('image');
    static $SEARCH_ATTRIBUTES = array('text','button','button-type');
    static $NAVIGATION_ATTRIBUTES = array('type','default','more');
    
    static $TEMPLATE_STATUS_ACTIVE = '0';
    static $TEMPLATE_STATUS_INACTIVE = '1';
    
    static $TEMPLATE_PUBLISHED = '0';
    static $TEMPLATE_NOT_PUBLISHED = '1';
    static $TEMPLATE_DRAFT = '2';
	static $DROP_DOWN = array(	'Basic layout'=>array('#canvas'=>'Window body',
													'#header-wrapper'=>'Header wrapper',
													'#body-wrapper'=>'Body wrapper',
													'#footer-wrapper'=>'Footer wrapper'),
								'Header'=>array('#logo'=>'Store logo',
												'#search .input input'=>'Search box',
												'#navigation'=>'Navigation bar',
												'#navigation .container'=>'Bar Container',
												'#navigation .container ul li a'=>'Nav Bar Links',
												'#navigation .container ul li a:hover'=>'Nav Bar Links(Hover)',
												'#navigation .container ul li ul li a'=>'Nav Bar Dropdown Links',
												'#navigation .container ul li ul li a:hover'=>'Nav Bar Dropdown Links(Hover)'),
								'Block'=>array('Body Default'=>array('#body-container .blocktitle'=>'Block Title',
																'#body-container .blockcontent'=>'Block Container',
																'#body-container .blockcontent img'=>'Image',
																'#body-container .standardtext'=>'Block Text',
																'#body-container .blockcontent .title a'=>'Content Link',
																'#body-container .blockcontent .title a:hover'=>'Content Link(Hover)'),
											   'Main Body Block'=>array('#main-body-block .blocktitle'=>'Block Title',
																		'#main-body-block .blockcontent'=>'Block Container',
																		'#main-body-block .blockcontent img'=>'Image',
																		'#main-body-block .blockcontent .contenttext'=>'Block Text',
																		'#main-body-block .blockcontent .title a'=>'Content Link',
																		'#main-body-block .blockcontent .title a:hover'=>'Content Link(Hover)'),
											   'Left Body Block'=>array('#left-body-block .blocktitle'=>'Block Title',
																		'#left-body-block .blockcontent'=>'Block Container',
																		'#left-body-block .blockcontent img'=>'Image',
																		'#left-body-block .blockcontent .contenttext'=>'Block Text',
																		'#left-body-block .blockcontent .title a'=>'Content Link',
																		'#left-body-block .blockcontent .title a:hover'=>'Content Link(Hover)'),
											   'Right Body Block'=>array('#right-body-block .blocktitle'=>'Block Title',
																		 '#right-body-block .blockcontent'=>'Block Container',
																		 '#right-body-block .blockcontent img'=>'Image',
																		 '#right-body-block .blockcontent .contenttext'=>'Block Text',
																		 '#right-body-block .blockcontent .title a'=>'Content Link',
																		 '#right-body-block .blockcontent .title a:hover'=>'Content Link(Hover)'),
																		 
												'Footer Default'=>array('#footer .blocktitle'=>'Block Title',
																		'#footer .blockcontent'=>'Block Container',
																		'#footer img'=>'Image',
																		'#footer .contenttext'=>'Block Text',
																		'#footer a'=>'Link',
																		'#footer a:hover'=>'Link(Hover)'
																		)),
								'Content'=>array('h1'=>'Heading 1 (h1)',
												 'h2'=>'Heading 2 (h2)',
												 'h3'=>'Heading 3 (h3)',
												 'h4'=>'Heading 4 (h4)',
												 'h5'=>'Heading 5 (h5)',
												 'h6'=>'Heading 6 (h6)',
												 '.categorytitle'=>'Category Title',
												 '.brandtitle'=>'Brand Title',
												 '.producttitle'=>'Product Title',
												 '.mrpsmall'=>'MRP (Small)',
												 '.mrplarge'=>'MRP (Large)',
												 '.srpsmall'=>'SRP (Small)',
												 '.srplarge'=>'SRP (Large)',
												 '.shortdescription'=>'Short Description',
												 '.empty'=>'Empty (No Content)',
												 '.standardtext'=>'Standard Text (All usage) ',
												 '.sectionbreak'=>'Section Break',
												 '.variantfield'=>'Variant Field',
												 '.variantvalue'=>'Variant Value',
												 '.featurefield'=>'feature Field',
												 '.featurevalue'=>'feature Value',
												 '.wishlist a'=>'Add to Wishlist',
												 '.wishlist a:hover'=>'Add to Wishlist (Hover)',
												 '.compare a'=>'Add to Compare',
												 '.compare a:hover'=>'Add to Compare (Hover)',
												 'a'=>'Body Links',
												 'a:hover'=>'Body Links (Hover)',
												 '#footer .blockcontent a'=>'Footer Links',
												 '#footer .blockcontent a:hover'=>'Footer Links (Hover)',
												 '.actionlink a'=>'Action Links',
												 '.actionlink a:hover'=>'Action Links (Hover)',
												 '.breadcrumb a'=>'Breadcrumb Link',
												 '.breadcrumb a:hover'=>'Breadcrumb Link(Hover)',
												  '.breadcrumbselected'=>'Breadcrumb (Selected)'),
								'Body'=>array('Popular categories'=>array(  '#popular-categories .blocktitle'=>'Block Title',
																		 '#popular-categories .blockcontent'=>'Block container',
																		 '#popular-categories .blockcontent img'=>'Image',
																		 '#popular-categories .blockcontent .contenttext'=>'Block Text',
																		 '#popular-categories .blockcontent a'=>'Content Link',
																		 '#popular-categories .blockcontent a:hover'=>'Content Link(On Hover)'),
										  'Popular brands'=>array(		 '#popular-brands .blocktitle'=>'Block Title',
																		 '#popular-brands .blockcontent'=>'Block container',
																		 '#popular-brands .blockcontent .contenttext'=>'Block Text',
																		 '#popular-brands .blockcontent img'=>'Image',
																		 '#popular-brands .blockcontent a'=>'Content Link',
																		 '#popular-brands .blockcontent a:hover'=>'Content Link(On Hover)'),
										  'New products'=>array(		 '#new-products .blocktitle'=>'Block Title',
																		 '#new-products .blockcontent'=>'Block container',
																		 '#new-products  .blockcontent .contenttext'=>'Block Text',
																		 '#new-products .blockcontent img'=>'Image',
																		 '#new-products .blockcontent a'=>'Content Link',
																		 '#new-products .blockcontent a:hover'=>'Content Link(On Hover)',
																		 ),
											
											'Featured products'=>array(  '#featured-products .blocktitle'=>'Block Title',
																		 '#featured-products .blockcontent'=>'Block container',
																		 '#featured-products .blockcontent .contenttext'=>'Block Text',
																		 '#featured-products .blockcontent img'=>'Image',
																		 '#featured-products .blockcontent a'=>'Content Link',
																		 '#featured-products .blockcontent a:hover'=>'Content Link(On Hover)',
																		 
																		 ),
											'Top selling products'=>array (
																		 '#current-top-sellers .blocktitle'=>'Block Title',
																		 '#current-top-sellers .blockcontent'=>'Block container',
																		 '#current-top-sellers .blockcontent .contenttext'=>'Block Text',
																		 '#current-top-sellers .blockcontent img'=>'Image',
																		 '#current-top-sellers .blockcontent a'=>'Content Link',
																		 '#current-top-sellers .blockcontent a:hover'=>'Content Link(On Hover)',
																		 
																		 ),
											'Recommended products'=>array('#recommended-products .blocktitle'=>'Block Title',
																		 '#recommended-products .blockcontent'=>'Block container',
																		 '#recommended-products .blockcontent img'=>'Image',
																		 '#recommended-products .blockcontent .contenttext'=>'Block Text',
																		 '#recommended-products .blockcontent a'=>'Content Link',
																		 '#recommended-products .blockcontent a:hover'=>'Content Link(On Hover)',
																		 ),
											'Recently View Products'=>array('#recently-viewed .blocktitle'=>'Block Title',
																		 '#recently-viewed .blockcontent'=>'Block container',
																		 '#recently-viewed .blockcontent .contenttext'=>'Block Text',
																		 '#recently-viewed .blockcontent img'=>'Image',
																		 '#recently-viewed .blockcontent a'=>'Content Link',
																		 '#recently-viewed .blockcontent a:hover'=>'Content Link(On Hover)',
																		 
																		 ),
											'Compare products'=>array(   '#compare-products .blocktitle'=>'Block Title',
																		 '#compare-products .blockcontent'=>'Block container',
																		 '#compare-products .blockcontent .contenttext'=>'Block Text',
																		 '#compare-products .blockcontent img'=>'Image',
																		 '#compare-products .blockcontent a'=>'Content Link',
																		 '#compare-products .blockcontent a:hover'=>'Content Link(Hover)'),
											'About the store'=>array(    '#about-store .blocktitle'=>'Block Title',
																		 '#about-store .blockcontent'=>'Block container',
																		 '#about-store .blockcontent .contenttext'=>'Block Text',
																		 '#about-store .blockcontent img'=>'Image',
																		 '#about-store .blockcontent a'=>'Content Link',
																		 '#about-store .blockcontent a:hover'=>'Content Link (Hover)'
																		 )),
								'Footer'=>array('Store Info'=>array(     '#store-info .blocktitle'=>'Block Title',
																		 '#store-info .blockcontent'=>'Block container',
																		 '#store-info .blockcontent .contenttext'=>'Block Text',
																		 '#store-info .blockcontent img'=>'Image',
																		 '#store-info .blockcontent a'=>'Link',
																		 '#store-info .blockcontent a:hover'=>'Link(On Hover)'),
												'Footer Categories'=>array('#footer-categories .blocktitle'=>'Block Title',
																		 '#footer-categories .blockcontent'=>'Block container',
																		 '#footer-categories .blockcontent .contenttext'=>'Block Text',
																		 '#footer-categories .blockcontent img'=>'Image',
																		 '#footer-categories .blockcontent a'=>'Link',
																		 '#footer-categories .blockcontent a:hover'=>'Link(On Hover)'),
												'Footer Brands'=>array('#footer-brands .blocktitle'=>'Block Title',
																		 '#footer-brands .blockcontent'=>'Block container',
																		 '#footer-brands .blockcontent .contenttext'=>'Block Text',
																		 '#footer-brands .blockcontent img'=>'Image',
																		 '#footer-brands .blockcontent a'=>'Link',
																		 '#footer-brands .blockcontent a:hover'=>'Link(On Hover)'),
												'Payment Method badge'=>array('#payment-methods .blocktitle'=>'Block Title',
																		 '#payment-methods .blockcontent'=>'Block container'),
												'Safe & Secure badge'=>array('#safe-secure .blocktitle'=>'Block Title',
																		 '#safe-secure .blockcontent'=>'Block container'),
												'Follow Us badge'=>array('#follow-us .blocktitle'=>'Block Title',
																		 '#follow-us .blockcontent'=>'Block container'),
												'Attribution Text'=>array('#footer-copyright .blockcontent'=>'Block container')
								)
							);	
	/*static $DROP_DOWN = array('Basic layout'=>array('#canvas'=>'Window body',
													#header-wrapper'=>'Header wrapper',
													#body-wrapper'=>'Body wrapper',
													#footer-wrapper'=>'Footer wrapper'),
								Block'=>array('Default'=>array('#container .heading'=>'Block Title',
																#container .content'=>'Block Container'),
											   Main Body Block'=>array('#main-body-block .heading'=>'Block Title',
																		#main-body-block .content'=>'Block Container'),
											   Left Body Block'=>array('#left-body-block .heading'=>'Block Title',
																		#left-body-block .content'=>'Block Container'),
											   Right Body Block'=>array('#right-body-block .heading'=>'Block Title',
																		 #right-body-block .content'=>'Block Container')),
								Content'=>array('#navigation .container ul li a'=>'Navigation Bar Links',
												 #navigation .container ul li a:hover'=>'Navigation Bar Links(On Hover)',
												 .content .title a'=>'Content Title',
												 .content .title a:hover'=>'Content Title(On Hover)',
												 .content img'=>'Image',
												 .content .desc'=>'Description',
												 .content .srp'=>'SRP',
												 .content .mrp'=>'MRP',
												 .content'=>'Text',	
												 .content a'=>'Links',
												 .content a:hover'=>'Links(On Hover)',
												 .content .addwishlink a'=>'Add to wishlist Link',
												 .content .addwishlink a:hover'=>'Add to wishlist Link(On Hover)',
												 .content .addcomparelink a'=>'Add to compare Link',
												 .content .addcomparelink a:hover'=>'Add to compare Link(On Hover)'),
								Header'=>array('#logo'=>'Store logo',
												#search input'=>'Search box',
												#navigation'=>'Navigation bar'),
								Body'=>array('Popular categories'=>array(   '#popular-categories .heading'=>'Block Title',
																		     #popular-categories .content'=>'Block container',
																			 #popular-categories .content img'=>'Image',
																			 #popular-categories .content a'=>'Title',
																			 #popular-categories .content a:hover'=>'Title(On Hover)'),
											  Popular brands'=>array(		 '#popular-brands .heading'=>'Block Title',
																		     #popular-brands .content'=>'Block container',
																			 #popular-categories .content img'=>'Image',
																			 #popular-brands .content a'=>'Title',
																			 #popular-brands .content a:hover'=>'Title(On Hover)'),
											  Popular Tags'=>array(		 '#popular-tags .heading'=>'Block Title',
																		     #popular-tags .content'=>'Block container',
																			 #popular-tags .content a'=>'Title',
																			 #popular-tags .content a:hover'=>'Title(On Hover)'),
											  New products'=>array(		 '#new-products .heading'=>'Block Title',
																		     #new-products .content'=>'Block container',
																		     #new-products .content .title a'=>'Content Title',
																			 #new-products .content .title a:hover'=>'Content Title(On Hover)',
																			 #new-products .content img'=>'Image',
																			 #new-products .content .desc'=>'Description',
																			 #new-products .content .srp'=>'SRP',
																			 #new-products .content .mrp'=>'MRP',
																			 #new-products .content a'=>'Links',
																			 #new-products .content a:hover'=>'Links(On Hover)',
																			 #new-products .content .addwishlink a'=>'Add to wishlist Link',
																			 #new-products .content .addwishlink a:hover'=>'Add to wishlist Link(On Hover)',
																			 #new-products .content .addcomparelink a'=>'Add to compare Link',
																			 #new-products .content .addcomparelink a:hover'=>'Add to compare Link(On Hover)'),
												
												Featured products'=>array(  '#featured-products .heading'=>'Block Title',
																		     #featured-products .content'=>'Block container',
																		     #featured-products .content .title a'=>'Content Title',
																			 #featured-products .content .title a:hover'=>'Content Title(On Hover)',
																			 #featured-products .content img'=>'Image',
																			 #featured-products .content .desc'=>'Description',
																			 #featured-products .content .srp'=>'SRP',
																			 #featured-products .content .mrp'=>'MRP',
																			 #featured-products .content a'=>'Links',
																			 #featured-products .content a:hover'=>'Links(On Hover)',
																			 #featured-products .content .addwishlink a'=>'Add to wishlist Link',
																			 #featured-products .content .addwishlink a:hover'=>'Add to wishlist Link(On Hover)',
																			 #featured-products .content .addcomparelink a'=>'Add to compare Link',
																			 #featured-products .content .addcomparelink a:hover'=>'Add to compare Link(On Hover)'),
												Top selling products'=>array('#current-top-sellers .heading'=>'Block Title',
																		     #current-top-sellers .content'=>'Block container',
																		     #current-top-sellers .content .title a'=>'Content Title',
																			 #current-top-sellers .content .title a:hover'=>'Content Title(On Hover)',
																			 #current-top-sellers .content img'=>'Image',
																			 #current-top-sellers .content .desc'=>'Description',
																			 #current-top-sellers .content .srp'=>'SRP',
																			 #current-top-sellers .content .mrp'=>'MRP',
																			 #current-top-sellers .content a'=>'Links',
																			 #current-top-sellers .content a:hover'=>'Links(On Hover)',
																			 #current-top-sellers .content .addwishlink a'=>'Add to wishlist Link',
																			 #current-top-sellers .content .addwishlink a:hover'=>'Add to wishlist Link(On Hover)',
																			 #current-top-sellers .content .addcomparelink a'=>'Add to compare Link',
																			 #current-top-sellers .content .addcomparelink a:hover'=>'Add to compare Link(On Hover)'),
												Recommended products'=>array('#recommended-products .heading'=>'Block Title',
																		     #recommended-products .content'=>'Block container',
																		     #recommended-products .content .title a'=>'Content Title',
																			 #recommended-products .content .title a:hover'=>'Content Title(On Hover)',
																			 #recommended-products .content img'=>'Image',
																			 #recommended-products .content .desc'=>'Description',
																			 #recommended-products .content .srp'=>'SRP',
																			 #recommended-products .content .mrp'=>'MRP',
																			 #recommended-products .content a'=>'Links',
																			 #recommended-products .content a:hover'=>'Links(On Hover)',
																			 #recommended-products .content .addwishlink a'=>'Add to wishlist Link',
																			 #recommended-products .content .addwishlink a:hover'=>'Add to wishlist Link(On Hover)',
																			 #recommended-products .content .addcomparelink a'=>'Add to compare Link',
																			 #recommended-products .content .addcomparelink a:hover'=>'Add to compare Link(On Hover)'),
												Recently View Products'=>array('#recently-viewed .heading'=>'Block Title',
																		     #recently-viewed .content'=>'Block container',
																		     #recently-viewed .content .title a'=>'Content Title',
																			 #recently-viewed .content .title a:hover'=>'Content Title(On Hover)',
																			 #recently-viewed .content img'=>'Image',
																			 #recently-viewed .content .desc'=>'Description',
																			 #recently-viewed .content .srp'=>'SRP',
																			 #recently-viewed .content .mrp'=>'MRP',
																			 #recently-viewed .content a'=>'Links',
																			 #recently-viewed .content a:hover'=>'Links(On Hover)',
																			 #recently-viewed .content .addwishlink a'=>'Add to wishlist Link',
																			 #recently-viewed .content .addwishlink a:hover'=>'Add to wishlist Link(On Hover)',
																			 #recently-viewed .content .addcomparelink a'=>'Add to compare Link',
																			 #recently-viewed .content .addcomparelink a:hover'=>'Add to compare Link(On Hover)'),
												Compare products'=>array(   '#compare-products .heading'=>'Block Title',
																		     #compare-products .content'=>'Block container',
																		     #compare-products .content .title a'=>'Content Title',
																			 #compare-products .content .title a:hover'=>'Content Title(On Hover)',
																			 #compare-products .content img'=>'Image',
																			 #compare-products .content a'=>'Links',
																			 #compare-products .content a:hover'=>'Links(On Hover)'),
												About the store'=>array(    '#about-store .heading'=>'Block Title',
																		     #about-store .content'=>'Block container')),
									Footer'=>array('Store Info'=>array(     '#store-info .heading'=>'Block Title',
																		     #store-info .content'=>'Block container',
	 																		 #store-info .content a'=>'Title',
																			 #store-info .content a:hover'=>'Title(On Hover)'),
													Footer Categories'=>array('#footer-categories .heading'=>'Block Title',
																		     #footer-categories .content'=>'Block container',
	 																		 #footer-categories .content a'=>'Title',
																			 #footer-categories .content a:hover'=>'Title(On Hover)'),
													Footer Brands'=>array('#footer-brands .heading'=>'Block Title',
																		     #footer-brands .content'=>'Block container',
	 																		 #footer-brands .content a'=>'Title',
																			 #footer-brands .content a:hover'=>'Title(On Hover)'),
													Footer Tags'=>array('#footer-tags .heading'=>'Block Title',
																		     #footer-tags .content'=>'Block container',
	 																		 #footer-tags .content a'=>'Title',
																			 #footer-tags .content a:hover'=>'Title(On Hover)'),
													Payment Method badge'=>array('#payment-methods .heading'=>'Block Title',
																		     #payment-methods .content'=>'Block container'),
													Safe & Secure badge'=>array('#safe-secure .heading'=>'Block Title',
																		     #safe-secure .content'=>'Block container'),
													Follow Us badge'=>array('#follow-us .heading'=>'Block Title',
																		     #follow-us .content'=>'Block container'),
													Attribution Text'=>array('#footer-copyright .content'=>'Block container')
									)
					);	*/
	
	static $DEFINED_BLOCKS = array('logo'=>'Store logo','search'=>'Search box','navigation'=>'Navigation bar');
    
	static $LAYOUT_LEFT_MAIN_RIGHT = '1';
	static $LAYOUT_LEFT_MAIN = '2';
	static $LAYOUT_MAIN_RIGHT = '3';
	static $LAYOUT_LEFT_RIGHT_MAIN= '4';
	static $LAYOUT_MAIN_LEFT_RIGHT = '5';
	static $LAYOUT_MAIN = '6';
	
	static $LAYOUT_RATIO = array(1=>array('left-body-block'=>25,'main-body-block'=>50,'right-body-block'=>25),
								 2=>array('left-body-block'=>40,'main-body-block'=>60,'right-body-block'=>0),
								 3=>array('left-body-block'=>0,'main-body-block'=>60,'right-body-block'=>40),
								 4=>array('left-body-block'=>25,'main-body-block'=>50,'right-body-block'=>25),
								 5=>array('left-body-block'=>25,'main-body-block'=>50,'right-body-block'=>25),
								 6=>array('left-body-block'=>0,'main-body-block'=>100,'right-body-block'=>0));
	
    static $FONT_SIZE = array('start_point'=>0,'end_point'=>25);
	static $LETTER_SPACING = array('start_point'=>0,'end_point'=>35);
	static $LINE_HEIGHT = array('start_point'=>0,'end_point'=>30);
	static $BORDER_SIZE = array('start_point'=>0,'end_point'=>5);
	static $BG_TOP = array('start_point'=>0,'end_point'=>60);
	static $BG_LEFT = array('start_point'=>0,'end_point'=>75);
	static $BG_RIGHT = array('start_point'=>0,'end_point'=>70);
	static $MARGIN = array('start_point'=>0,'end_point'=>15);
	static $MARGIN_RIGHT = array('start_point'=>0,'end_point'=>30);
	static $MARGIN_LEFT = array('start_point'=>0,'end_point'=>30);
	static $MARGIN_BOTTOM = array('start_point'=>0,'end_point'=>30);
	static $PADDING_TOP = array('start_point'=>0,'end_point'=>35);
	static $PADDING_LEFT = array('start_point'=>0,'end_point'=>35);
	static $PADDING_BOTTOM = array('start_point'=>0,'end_point'=>35);
	static $PADDING_RIGHT = array('start_point'=>0,'end_point'=>35);
	static $WIDTH = array('start_point'=>0,'end_point'=>150);
	static $HEIGHT = array('start_point'=>0,'end_point'=>150);

	static $ORDER_BY = array('Default logic','Custom logic');
	static $DISPLAY_COUNT = array('All','8','12','16','20');
	static $VISIBILITY = array('Visible','Hidden');
	static $VISIBILITY_FOR_CUSTOM = array('0' => 'Visible','1' => 'Hidden');
	static $UNDEFINED_LOCATION = 'undefined (admin status)';

	static $BODY_LOCATIONS = array('left-body-block' => 'Left body block',
									'main-body-block' => 'Main body block',
									'right-body-block' => 'Right body block',
									'body-container'	=>	'Body top',
									'body-container'	=>	'Body bottom',
									'header'			=>	'Header',
									'footer'			=>	'Footer'
								);
							
	static $CONTENT_BLOCKS = array('logo'					=>		'logo',
	 							   'search'					=>		'search',
								   'navigation'				=>		'navigation',
								   'goo2o'					=>		'goo2o',
								   'popular-categories'		=>		'popular-categories',
								   'popular-brands' 		=> 		'popular-brands',
								   'product'				=>		'new-products',
								   'featured'				=>		'featured-products',
								   'topselling'				=>		'current-top-sellers',
								   'tagcloud'				=>		'popular-tags',
								   'recomended'				=>		'recommended-products',
								   'recentlyviewed'			=>		'recently-viewed',
								   'compare'				=>		'compare-products',
								   'bodyaboutthestore'		=>		'about-store',
								   'bodynewsletter'			=>		'news-letter',
								   'footernewsletter'		=>		'footer-news-letter',
								   'socialwidget'			=>		'follow-us',
								   'footeraboutthestore'	=>		'footer-about-store',
								   'paymentmethod'			=>		'payment-methods',
								   'safesecure'				=>		'safe-secure',
								   'footerlinkspowered_link'=>		'powered-by',
								   'footerlinkscategory'	=>		'footer-categories',
								   'footerlinksbrand'		=>		'footer-brands',
								   'footerlinkstag'			=>		'footer-tags',
								   'pagesotherlinks'		=>		'store-info',
								   'footercopyright'		=>		'footer-copyright'					
								  
								);
								
	static $CONTENT_BLOCKS_TITLE = array('logo'				=>		'logo',
	 							   'search'					=>		'search',
								   'navigation'				=>		'navigation',
								   'popular-categories'		=>		'Popular Categories',
								   'popular-brands' 		=> 		'Popular Brands',
								   'new-products'			=>		'New Products',
								   'featured-products'		=>		'Featured Products',
								   'current-top-sellers'	=>		'Current Top Sellers',
								   'popular-tags'			=>		'Popular Tags',
								   'recommended-products'	=>		'Recommended Products',
								   'recently-viewed'		=>		'Recently viewed Products',
								   'compare-products'		=>		'Compare Products',
								   'about-store'			=>		'About The Store',
								   'news-letter'			=>		'News Letter',
								   'footer-news-letter'		=>		'Footer News Letter',
								   'follow-us'				=>		'Follow Us',
								   'footer-about-store'		=>		'Footer About Store',
								   'payment-methods'		=>		'Payment Methods',
								   'safe-secure'			=>		'Safe Secure',
								   'powered-by'				=>		'Powered By',
								   'footer-categories'		=>		'Footer Categories',
								   'footer-brands'			=>		'Footer Brands',
								   'footer-tags'			=>		'Footer Tags',
								   'store-info'				=>		'Store Info',
								   'footer-copyright'		=>		'Attribution Text'					
								  
								);
								
	static $BODY_CONTENT_VARS = array(
								   'popular-categories'		=>		'_popular_categories_content',
								   'popular-brands' 		=> 		'_popular_brands_content',
								   'new-products'			=>		'_product_content',
								   'featured-products'		=>		'_featured_product_content',
								   'current-top-sellers'	=>		'_topselling_product_content',
								  // 'popular-tags'			=>		'_tagcloud_content',
								   'recommended-products'	=>		'_recomended_product_content'
								   //'recently-viewed'		=>		'_recently_viewed_content'
								);
								
	static $FOOTER_CONTENT_VARS = array(
								   'powered-by'				=>		'_powered_link_content',
								   'footer-categories'		=>		'_footer_link_category_contents',
								   'footer-brands'			=>		'_footer_link_brand_contents',
								  // 'footer-tags'			=>		'_footer_link_tag_contents',
								   'store-info'				=>		'_visible_store_info_content'
								);
	static $POPULATE_CONTENT_FUNCTIONS = array(
									'popular-categories'	=>		'populateCategoryContents',
								   'popular-brands' 		=> 		'populateBrandContents',
								   'new-products'			=>		'populateProductContents',
								   'featured-products'		=>		'populateFeaturedProductContents',
								   'current-top-sellers'	=>		'populateTopSellingProductContents',
								  // 'popular-tags'			=>		'populateTagCloudContents',
								   'recommended-products'	=>		'populateRecomendedProductContents',
								  // 'recently-viewed'		=>		'populateRecentlyViewedProductContents',
								   'powered-by'				=>		'populatePoweredLinkContents',
								   'footer-categories'		=>		'popultateFooterCategoryContents',
								   'footer-brands'			=>		'popultateFooterBrandContents',
								   //'footer-tags'			=>		'popultateFooterTagcloudContents',
								   'store-info'				=>		'populateStoreInfoContents'
								  
								  );
								  
	static $BLOCKS_WITH_CLASSES = array('new-products', 'featured-products', 'current-top-sellers','recommended-products','recently-viewed','compare-products');
									
								
	
	static $LOGO_FONT_TYPE = array('ALGER.TTF'=>'Algerian','arial.ttf'=>'Arial','ARLRDBD.TTF'=>'Arial rounded MT bold','BAUHS93.TTF'=>'Bauhaus 93','BOD_R.TTF'=>'Bodoni MT black','BRITANIC.TTF'=>'Britannic bold','BROADW.TTF'=>'Brodway','CALIBRI.TTF'=>'Calibri','CAMBRIA.TTC'=>'Cambria','CONSTAN.TTF'=>'Constania','cour.ttf'=>'Courier new','FRADM.TTF'=>'Franklin gothic demi','impact.ttf'=>'Impact','INFROMAN.TTF'=>'Informal roman','LCALLIG.TTF'=>'Lucida calligraphy','MATURASC.TTF'=>'Matura','MSMINCHO.TTF'=>'MS mincho','MTCORSVA.TTF'=>'Monotype consiva','ROCK.TTF'=>'Rockwell','tahoma.ttf'=>'Tahoma','TEMPSITC.TTF'=>'Tempus sans','times.ttf'=>'Times new Roman','trebuc.ttf'=>'Trebuchet ms','verdana.ttf'=>'Verdana');
	static $LOGO_FONT_SIZE = array('25'=>'25', '30'=>'30', '35'=>'35', '40'=>'40', '45'=>'45', '50'=>'50');
	static $SEARCH_BUTTON = array('default'=>'Default button', 'no'=>'No button', 'search'=>'Search button', 'find'=>'Find button', 'go'=>'Go button');
	static $SEARCH_BUTTON_TYPE = array('straight_corner'=>'Straight corner', 'rounded_corner'=>'Rounded corner', 'oval'=>'Oval', 'circle'=>'Circle', 'arrow'=>'Arrow');
	
	static $NEW_PRODUCT_CAPTION = array('title'=>'New product Configuration',
										'description'=>'Configure it by defining the list of new products. You can define this product list both manually and automatically (based on default logic).',
										'option_heading'=>'New product option');
	static $FEATURED_PRODUCT_CAPTION = array('title'=>'Featured Product Configuration',
										'description'=>'Configure it by defining the list of new products. You can define this product list both manually and automatically (based on default logic).',
										'option_heading'=>'Featured product option');
	
	static $TOPSELLEING_PRODUCT_CAPTION = array('title'=>'Top Selling Product Configuration',
										'description'=>'Configure it by defining the list of new products. You can define this product list both manually and automatically (based on default logic).',
										'option_heading'=>'Top Selling product option');
	
	static $RECOMENDED_PRODUCT_CAPTION = array('title'=>'Recommended product Configuration',
										'description'=>'You can configure it by setting the count that you want to display on your storefront & changing the presentation style.',
										'option_heading'=>'New product option');
	
	static $RECENTLYVIEWED_PRODUCT_CAPTION = array('title'=>'Recently viewed product Configuration',
										'description'=>'You can configure it by setting the count that you want to display on your storefront & changing the presentation style.',
										'option_heading'=>'New product option');
	
	static $PAYMENT_METHOD = array('title'=>'Payment badge Configuration',
								   'description'=>'Configure the payment badge, it shows what all payment methods accepted on your store.');
	
	static $SAFE_SECURE = array('title'=>'Safe & secure badge Configuration',
								'description'=>'Configure the safe & secure badge, it shows the safe shopping environment on your store.');
										
	static $PAGE_TYPE = array('1'=>'Standard','2'=>'Custom','3'=>'Redirect');
	static $PAGE_LAYOUT = array('1'=>'1 column','2'=>'2 columns with left bar','3'=>'2 columns with right bar','4'=>'3 columns ');
	static $PAGE_BORDER_DROPDOWN = array('none'=>'None','full'=>'Full','bottom'=>'Bottom','right'=>'Right','left'=>'Left','top'=>'Top','top_bottom'=>'Top & bottom','left_right'=>'Left & right');
	static $PAGE_BORDER_STYLE_DDOWN = array('none'=>'None','solid'=>'Solid','dotted'=>'Dotted','dashed'=>'Dashed');
	static $PAGE_BORDER_SIZE = array('0'=>'0px','1'=>'1px','2'=>'2px','3'=>'3px','4'=>'4px','5'=>'5px');
	static $SMALLEST_FONTSIZE = array(11,12,13,14,15,16,17);
	static $LARGEST_FONTSIZE = array(18,19,20,21,22,23,24);
	static $SHOW_ME_TYPE = array('all'=>'All' , '1'=>'Standard' , '2'=>'Custom' , '3'=>'Redirect to');
	static $VISIBILITY_STATUS = array('all'=>'All' , '1'=>'Visible' , '2'=>'Hidden');
	static $BANNER_LOCATION = array('1'=>'Header top' , '2'=>'Body top' , '3'=>'Body bottom' , '4'=>'Main body block' , '5'=>'Left body block' , '6'=>'Right body block' , '7'=>'Footer bottom');
	//static $BANNER_PAGES = array('1'=>'Home page','2'=>'Category page','3'=>'Brand page','4'=>'Search result page');
	static $BANNER_PAGES = array('1'=>'Home page');
	static $FOLLOW_US_DISPLAY_STYLE = array('Show service icon only','Show text links only','Show service icon with text link');
	static $SKIN = array('Default','Light skin','Dark skin');
	
	static $CUSTOM_BLOCK_TYPES = array('0'=>'Product','1'=>'Category','2'=>'Brand','3'=>'Custom');
	static $CUSTOM_BLOCK_POSITIONS = array('2'=>'Main body block','3'=>'Left body block','4'=>'Right body block','5'=>'Body top','6'=>'Body bottom');
	static $CUSTOM_BLOCK_POSITIONS_FOR_CUSTOM = array('0'=>'Header','1'=>'Footer','2'=>'Main body block','3'=>'Left body block','4'=>'Right body block','5'=>'Body top','6'=>'Body bottom');
	static $CUSTOM_BLOCK_STRUCTURE = array('image-only'=>'Image only','image-with-title'=>'Image with title','image-with-description'=>'Image with title and description','list'=>'List','grid'=>'Grid');
	static $CUSTOM_SHOW_ON_PAGE = array('home-page'=>'Home page');
	//static $CUSTOM_SHOW_ON_PAGE = array('home-page'=>'Home page','category-page'=>'Category page','brand-page'=>'Brand page','search-result-page'=>'Search result page');
	static $CUSTOM_BLOCK_EFFECT = array('auto-scroller'=>'Auto scroller','scroll-on-click'=>'Scroll on click','both'=>'Both','none'=>'None');
	
	static $SMARTY_BLOCKS_ARRAY = array('navigation'		=>		'navigation_data',
								   'popular-categories'		=>		'popular_categories_array',
								   'popular-brands' 		=> 		'popular_brands_array',
								   'new-products'			=>		'new_products_array',
								   'featured-products'		=>		'featured_products_array',
								   'current-top-sellers'	=>		'current_top_sellers_array',
								   'popular-tags'			=>		'popular_tags_array',
								   'recommended-products'	=>		'recommended_products_array',
								   'recently-viewed'		=>		'recently_viewed_array',
								   'compare-products'		=>		'compare_products_array',
								   'powered-by'				=>		'powered_by_array',
								   'footer-categories'		=>		'footer_categories_array',
								   'footer-brands'			=>		'footer_brands_array',
								   'footer-tags'			=>		'footer_tags_array',
								   'store-info'				=>		'store_info_array'					
								  
								);
	static $CUSTOM_BLOCK_POSITION_IN_TPL = array('Header'=>'header.tpl','Footer'=>'footer.tpl','Main body block'=>'main-body-block.tpl','Left body block'=>'left-body-block.tpl','Right body block'=>'right-body-block.tpl','Body top'=>'index.tpl','Body bottom'=>'index.tpl');
	static $BANNER_POSITION_IN_TPL = array('1'=>'index.tpl','2'=>'index.tpl','3'=>'index.tpl','4'=>'main-body-block.tpl','5'=>'left-body-block.tpl','6'=>'right-body-block.tpl','7'=>'index.tpl');
	static $TEMPLATE_PATH ='/modules/default/views/scripts/templates/';
	static $STANDARD_PAGE_BORDER = array('none'=>'None','all'=>'All','bottom'=>'Bottom','right'=>'Right','left'=>'Left','top'=>'Top','top_bottom'=>'Top & bottom','left_right'=>'Left & right');
	static $PAGE_FONT_STYLE = array('normal'=>'Normal','bold'=>'Bold','italic'=>'Italic');
	static $PAGE_FONT_SIZE = array('8'=>'8px','9'=>'9px','10'=>'10px','11'=>'11px','12'=>'12px','14'=>'14px','16'=>'16px','18'=>'18px','20'=>'20px','22'=>'22px','24'=>'24px','26'=>'26px','28'=>'28px','36'=>'36px','48'=>'48px','72'=>'72px');
	static $LOGO_TYPES = array('text_logo'=>'Text Logo','image_logo'=>'Image Logo');
	
	static $SERVER_NAME = '';
	
	static $JAVASCRIPT_TYPE = array('plugin'=>'0','jscript'=>'1');
	
	static $ADVANCE_TEMPLATE_CATAGORY = array('my_saved_design'=>'My saved templates','my_drafts'=>'My drafts');
	
	static $HTML_HELP_TEXT = array("home"=>"Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element. Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element.","header"=>" Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element. Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element.",
"footer"=>" Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element. Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element.");
	
	static $CSS_HELP_TEXT = array("common"=>" Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element. Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element.",
								"header"=>" Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element. Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element.",
								"footer"=>" Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element. Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element.",
								"body"=>" Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element. Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element.",
								"print"=>" Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element. Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element.",
								"custom"=>" Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element. Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element.",
								"404"=>" Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element. Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element.");
	
	static $JS_HELP_TEXT = array("new"=>" Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element. Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element.",
								"old"=>" Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element. Dozens of professionally designed style variants from name-brand designers. All Squarespace styles allow point and click control over every design element.");
								
	static $TEMPLATE_STATUS_IN_PRACTICE = array('current' 		=> 'Current',
												'ready_to_use'  => 'Ready to use',
												'published' 	=> 'Published',
												'draft'			=>'Draft'
												);
	static $TEMPLATE_LIST_TYPE = array('all'=>'All','saved'=>'Saved','published'=>'Published','draft'=>'Draft');
	
	static $READY_TO_PUBLISH = array('ready'=>'0','not_ready'=>'1');
	
	static $GOO2O_POSITIONS = array('left_align'=>'Left align','right_align'=>'Right align','custom'=>'Custom');
	static $GOO2O_BLOCK_SELECTION = array('0'=>'Open left side','1'=>'Open right side');
	static $FOOTER_ATTRIBUTION_HTML = 'All Rights Reserved. Powered by <a href="http://goo2ostore.com" title="goo2o.com">goo2o.com</a>';
	static $FOOTER_ATTRIBUTION_TEXT = 'All Rights Reserved. Powered by goo2o.com';
	static $GOO2O_POWERED_LINKS = array(1=>array('id'=>'1','title'=>'My Cart','url'=>'http://goo2ostore.com/cart/#list','visibility'=>'Visible'),
								2=>array('id'=>'2','title'=>'My Account','url'=>'http://goo2ostore.com/myaccount','visibility'=>'Visible'),
								3=>array('id'=>'3','title'=>'Track My Order','url'=>'http://goo2ostore.com/myaccount/buyer/#purchase-listing','visibility'=>'Visible'),
								4=>array('id'=>'4','title'=>'Safe Shopping','url'=>'http://support.goo2o.com/entries/506151-user-protection-policy','visibility'=>'Visible'),
								5=>array('id'=>'5','title'=>'Create Goo2o ID','url'=>'http://secure.goo2ostore.com/registration','visibility'=>'Visible')
					);
	
	
}