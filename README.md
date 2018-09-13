# Display Specific Products for Elementor Plugin
## Introduction
*Display Specific Products for Elementor*, or DSPE for short, is a Wordpress plugin which acts as an extension for the Elementor Page Builder plugin. The plugin adds a new Widget in the Elementor's edit screen, under the "general" Widgets group. The widget's title in the Elementor Widgets list is "Display Specific Products".

DSPE creates a grid of Woocommerce products, displaying the products' featured images and titles. The images and titles are clickable and link to each listing's product page.

## Installation
If you don't have Elementor Page Builder installed, [get it from here](https://wordpress.org/plugins/elementor/) and install it in your Wordpress site before installing DSPE.

To install *Display Specific Products for Elementor*: 
1. Clone this repository - save it as a `.zip` archive. Make sure it is named `display-specific-products-elementor.zip`.
2. In your Wordpress Dashboard, go to Plugins, and click on `Add New`.
3. Click on `Upload Plugin`, and upload the `display-specific-products-elementor.zip` file from step 1.
4. Click `Install`, and then click `Activate`.
5. You're done! Create a new page with Elementor or edit an existing one, and you will see the new widget in the Elementor widget screen.

## Using the plugin
Using *Display Specific Products for Elementor* is simple. Drag the widget into a section on your page, and you will see the plugin's settings screen in the Elementor side menu.

The plugin has the following settings fields:

- **Choose Products** - This is a smart dropdown input. When you click the input box, it opens a dropdown menu with all of your website's Woocommerce products. It has a handy autocomplete feature: Just start typing the names of the products you want to display with the plugin, and the dropdown menu will update to display only the relevant products. You can add as many products to the list as you like.
- **Products Per Row** - This dropdown menu enables you to choose how many products you want to display in each row of the grid. You can choose between 1-4.
- **Randomize Product Order** - If you want to display the products in a different order for each page load, keep this toggle as "On". It is "On" by default. When it is off, it displays the products in the order you chose them in the `Choose Products` input above.

Enjoy!
