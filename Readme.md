# Google Shopping Feed - Magento Module#

In need of selling products online, there is also need of using Google shopping service which makes easy for users to search for products on online shopping websites and compare prices.

With Google Shopping Feed user can manage standardized XML files required by Google shopping service.

Here is the overview of functionalities provided by Google Shopping Feed extension

* Easily create XML file choosing product categories
* Define path for XML file
* Manage additional XML product information using simple JSON format
* Set up periodic updates of created Xml files using Cron job scheduler

# Usage

### Creating Google Shopping Feed

Using descriptive fields user needs to
* set path with file name
* add title for feed
* add feed description (up to 5000 characters recommended by Google)
* add products to feed choosing and selecting categories
* additionally, add more attributes for products using JSON data format - those attributes can be based on existing product categories in Magento or adding new ones with default value.

### Available attribute json options
* attribute - Magento attribute code used to fetch field value
* type - field data type (‘integer’, ’string’, etc.), required if attribute is not already provided by Google Merchant Center (See [Products Feed Specification](https://support.google.com/merchants/answer/188494?hl=en&ref_topic=3404778))
* prefix - prefix for tags, usually it is 'g' for shopping feed
* default - default value for tag if attribute is not found in Magento attributes, in case that default value is not set and also there are no such category in Magentos' product categories tag will not be created
* value_prefix - custom prefix for attributes in tag

Example of JSON for adding new attributes:
```json
{
	"gender": {
		"default": "Unisex",
		"type": "string",
		"prefix": "g",
		"value_prefix": "shop-",
		"attribute": "kid_gender"
	},
	"size": {
		"prefix": "g",
		"attribute": "size"
	}
}
```

Example above submits two more attributes for XML file, `gender` and `size`.
Before creating gender tag in XML file, Google Shopping Feed checks attributes in Magento by `attribute` value (`kid_gender` for `gender` example and `size` for `size` example), if there are no such attribute, script fallbacks to `default` value (`Unisex` in `gender`, or in the `size` example tag won't be created).

### Example of XML file

```xml
<?xml version="1.0"?>
<rss xmlns:g="" version="2.0">
  <channel>
    <title>Title </title>
    <link>http://someshop.com</link>
    <description>Description of product</description>
    <item>
		<g:id>30004</g:id>
		<g:gender type="string">shopname-unisex</g:gender>
		<g:size>xl</g:size>
		<g:brand type="string">Brand</g:brand>
		<g:availability>available</g:availability>
		<g:condition>new</g:condition>
		<g:link>http://someshop/link-to-product</g:link>
		<g:image_link>http://junior-barneklaer.local/images/product-image.jpg</g:image_link>
		<g:price>USD 299.00</g:price>
    </item>
    <item>
		<g:id>30005</g:id>
		<g:gender type="string">shopname-male</g:gender>
		<g:size>xl</g:size>
		<g:availability>available</g:availability>
		<g:condition>new</g:condition>
		<g:link>http://someshop/link-to-product2</g:link>
		<g:image_link>http://junior-barneklaer.local/images/product2-image.jpg</g:image_link>
		<g:price>USD 299.00</g:price>
    </item>
  </channel>
</rss>
```

### Setting Cron job scheduler

Navigating to System -> Configuration and choosing in left menu Google Shopping will lead to Google Shopping Crone Scheduler where user need to set Frequency and Start Time for periodic generating existing Google Shopping Feed XML files.

### Google Shopping Feed screenshots

![form](https://s3-eu-west-1.amazonaws.com/stcd/stunt_mage_google_shopping_feed/edit-form.png "Edit form")
![grid](https://s3-eu-west-1.amazonaws.com/stcd/stunt_mage_google_shopping_feed/grid.png "Grid preview")
![cron](https://s3-eu-west-1.amazonaws.com/stcd/stunt_mage_google_shopping_feed/system-config.png "System config")

Copyright StuntCoders — [Start Your Online Store Now](http://stuntcoders.com/)
