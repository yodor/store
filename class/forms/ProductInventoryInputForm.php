<?php
include_once("lib/forms/InputForm.php");
include_once("lib/input/DataInputFactory.php");
include_once("class/beans/ProductsBean.php");
include_once("class/beans/ClassAttributesBean.php");
include_once("class/beans/ProductColorsBean.php");
include_once("class/beans/StoreSizesBean.php");
include_once("class/beans/InventoryAttributeValuesBean.php");

include_once("class/input/renderers/ClassAttributeField.php");
include_once("class/input/renderers/SourceRelatedField.php");


class ProductInventoryInputForm extends InputForm
{

    protected $prodID = -1;
    protected $product = array();

    public function __construct()
    {

        $field = DataInputFactory::Create(DataInputFactory::SELECT, "pclrID", "Цветова схема", 0);
        $colors = new ProductCategoriesBean();
        $field->getRenderer()->setIterator($colors->query());

        $field->getRenderer()->list_key = "pclrID";
        $field->getRenderer()->list_label = "color";
        $field->getValueTransactor()->renderer_source_copy_fields = array("color");
        $this->addField($field);


        $field = DataInputFactory::Create(DataInputFactory::SELECT, "size_value", "Оразмеряване", 0);
        $sizes = new StoreSizesBean();
        $field->getRenderer()->setIterator($sizes->query());
        $field->getRenderer()->list_key = "size_value";
        $field->getRenderer()->list_label = "size_value";

        $field->getRenderer()->addon_content = "<a class='ActionRenderer' action='inline-new' href='../../sizes/add.php'>" . tr("Нов код за оразмеряване") . "</a>";

        $this->addField($field);

        $field = DataInputFactory::Create(DataInputFactory::TEXT, "stock_amount", "Стокова наличност", 1);
        $this->addField($field);

        $field = DataInputFactory::Create(DataInputFactory::TEXT, "price", "Продажна цена", 0);
        $this->addField($field);

        $field = DataInputFactory::Create(DataInputFactory::TEXT, "buy_price", "Покупна цена", 0);
        $this->addField($field);

        $field = DataInputFactory::Create(DataInputFactory::TEXT, "old_price", "Стара цена", 0);
        $this->addField($field);

        $field = DataInputFactory::Create(DataInputFactory::TEXT, "weight", "Тегло", 0);
        $this->addField($field);

        $field = new ArrayDataInput("value", "Атрибути на класа", 0);
        $field->allow_dynamic_addition = false;
        $field->source_label_visible = true;


        $field->getValueTransactor()->process_datasource_foreign_keys = true;

        $bean1 = new InventoryAttributeValuesBean();
        $field->setSource($bean1);


        $rend = new SourceRelatedField();

        $bean = new ClassAttributesBean();
        $rend->setIterator($bean->query());

        $rend->list_key = "caID";
        $rend->list_label = "attribute_name";

        $field->setRenderer($rend);

        $this->addField($field);
    }

    public function setProductID($prodID)
    {
        $this->prodID = (int)$prodID;

        $this->getField("pclrID")->getRenderer()->getIterator()->select->where = " prodID='{$this->prodID}' ";

        $this->getField("pclrID")->getRenderer()->addon_content = "<a class='ActionRenderer' action='inline-new' href='../color_gallery/add.php?prodID={$this->prodID}'>" . tr("Нова цветова схема") . "</a>";

        // 	  $this->getField("size_value")->getRenderer()->setFilter(" WHERE prodID='{$this->prodID}' ");

        $prods = new ProductsBean();
        $this->product = $prods->getByID($this->prodID);


        $value_field = $this->getField("value");

        $rend = $value_field->getRenderer();

        $rend->setCaption(tr("Продуктов клас") . ": " . $this->product["class_name"]);


        $rend->getIterator()->select->where = " ca.class_name='". $this->product["class_name"]."' ";
        $rend->getIterator()->select->from.= " ca LEFT JOIN attributes attr ON attr.name = ca.attribute_name ";
        $rend->getIterator()->select->fields = " ca.*, attr.unit as attribute_unit, attr.type attribute_type ";

        $this->getField("price")->setValue($this->product["price"]);
        $this->getField("buy_price")->setValue($this->product["buy_price"]);
        $this->getField("old_price")->setValue($this->product["old_price"]);
        $this->getField("weight")->setValue($this->product["weight"]);

    }

    public function loadBeanData($editID, DBTableBean $bean)
    {

        $item_row = parent::loadBeanData($editID, $bean);

        $value_field = $this->getField("value");

        $rend = $value_field->getRenderer();

        $rend->getIterator()->select->from.=" ca LEFT JOIN inventory_attribute_values iav ON iav.caID = ca.caID AND iav.piID=$editID LEFT JOIN attributes attr ON attr.name = ca.attribute_name ";
        $rend->getIterator()->select->where = " ca.class_name='". $this->product["class_name"]."'";
        $rend->getIterator()->select->fields = " ca.*, iav.value, attr.unit as attribute_unit, attr.type attribute_type ";

    }

    public function loadPostData(array $arr) : void
    {
        parent::loadPostData($arr);

    }
}

?>
