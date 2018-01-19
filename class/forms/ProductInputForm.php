<?php
include_once ("lib/forms/InputForm.php");
include_once ("lib/input/InputFactory.php");

include_once ("class/beans/BrandsBean.php");
include_once ("class/beans/SectionsBean.php");

include_once ("class/beans/ProductClassesBean.php");
include_once ("class/beans/ProductCategoriesBean.php");
include_once ("class/beans/ProductFeaturesBean.php");
include_once ("class/beans/ProductPhotosBean.php");

include_once ("class/beans/ClassAttributeValuesBean.php");
include_once ("class/input/renderers/ClassAttributeField.php");
include_once ("lib/input/transactors/CustomFieldTransactor.php");



class ProductInputForm extends InputForm
{

    public function __construct()
    {

        $field = InputFactory::CreateField(InputFactory::SELECT, "section", "Секция", 1);
	$rend = $field->getRenderer();
	$rend->setSource(new SectionsBean());
	$rend->list_key="section_title";
	$rend->list_label="section_title";
	$this->addField($field);

	$field = InputFactory::CreateField(InputFactory::NESTED_SELECT, "catID", "Категория", 1);
	$bean1 = new ProductCategoriesBean();
	$rend = $field->getRenderer();
	$rend->setSource($bean1);
	$rend->list_key="catID";
	$rend->list_label="category_name";
            
	$this->addField($field);

	$field = InputFactory::CreateField(InputFactory::SELECT, "brand_name", "Марка", 1);
	$rend = $field->getRenderer();
	$rend->setSource(new BrandsBean());
	$rend->list_key="brand_name";
	$rend->list_label="brand_name";
	$this->addField($field);

	$field = InputFactory::CreateField(InputFactory::SELECT, "class_name", "Продуктов клас", 0);
	$rend = $field->getRenderer();
	$rend->setSource(new ProductClassesBean());
	$rend->list_key="class_name";
	$rend->list_label="class_name";
	$this->addField($field);


	$field = InputFactory::CreateField(InputFactory::TEXTFIELD, "product_name", "Име на продукта", 1);
	$this->addField($field);

//         $field = InputFactory::CreateField(InputFactory::TEXTFIELD, "product_code", "Product Code", 1);
// 	$this->addField($field);
	
	
// 	$field = InputFactory::CreateField(InputFactory::TEXTFIELD, "price", "Price", 0);
// 	$this->addField($field);
// 	
// 	$field = InputFactory::CreateField(InputFactory::TEXTFIELD, "buy_price", "Buy Price", 0);
// 	$this->addField($field);
// 
// 	$field = InputFactory::CreateField(InputFactory::TEXTFIELD, "old_price", "Old Price", 0);
// 	$this->addField($field);
// 	
// 	$field = InputFactory::CreateField(InputFactory::TEXTFIELD, "weight", "Weight", 0);
// 	$this->addField($field);

	
// 	$field = InputFactory::CreateField(InputFactory::TEXTFIELD, "stock_amount", "Stock Amount", 1);
// 	$this->addField($field);
	
	$field = InputFactory::CreateField(InputFactory::CHECKBOX, "visible", "Видим (в продажба)", 0);
	$this->addField($field);

// 	$field = InputFactory::CreateField(InputFactory::CHECKBOX, "promotion", "Promotion", 0);
// 	$this->addField($field);

	$field = InputFactory::CreateField(InputFactory::MCE_TEXTAREA, "product_summary", "Описание", 0);
	$this->addField($field);
	
	
// 	$field = InputFactory::CreateField(InputFactory::MCE_TEXTAREA, "product_description", "Product Description", 0);
// 	$this->addField($field);

	
	$field = InputFactory::CreateField(InputFactory::TEXTAREA, "keywords", "Ключови думи", 0);
	$this->addField($field);

	
// 	$input = InputFactory::CreateField(InputFactory::SESSION_IMAGE, "photo","Photo", 0);
// 	$input->setSource(new ProductPhotosBean());
// 	$input->transact_mode = InputField::TRANSACT_OBJECT;
// 	$input->getValueTransactor()->max_slots = 4;
// 	$this->addField($input);
	
	$field1 = new ArrayInputField("feature", "Характеристики", 0);
	$field1->allow_dynamic_addition = true;
	$field1->source_label_visible = true;

	$features_source = new ProductFeaturesBean();
	$field1->setSource($features_source);

	$renderer = new TextField();
	$renderer->setSource($features_source);
	$field1->setRenderer($renderer);

	$field1->setValidator(new EmptyValueValidator());
	$field1->setProcessor(new BeanPostProcessor());

	$this->addField($field1);


// 	$field = new ArrayInputField("value", "Optional Attributes", 0);
// 	$field->allow_dynamic_addition = false;
// 	$field->source_label_visible = true;
// 	$field->getValueTransactor()->process_datasource_foreign_keys = true;
// 
// 	$bean1 = new ClassAttributeValuesBean();
// 	$field->setSource($bean1);
// 
// 	$rend = new ClassAttributeField();
// 	$field->setRenderer($rend);
// 
// 	$this->addField($field);


  }
  public function loadBeanData($editID, DBTableBean $bean)
  {

      parent::loadBeanData($editID,  $bean);

//       $renderer = $this->getField("value")->getRenderer();
//       $renderer->setCategoryID($this->getField("catID")->getValue());
//       $renderer->setProductID($editID);

  }
  public function loadPostData(array $arr)
  {
      parent::loadPostData($arr);
      
//       $renderer = $this->getField("value")->getRenderer();
//       $renderer->setCategoryID($arr["catID"]);

  }
}
?>
