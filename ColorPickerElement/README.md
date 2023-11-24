**Color Picker Element** is a Magento 2 module with the following features:

1. `orange35ColorPickerElement` - uiClass for an input based on jQuery colorpicker. Shows chosen color as input background and shows color hex code as input text in contrast with background. 
2. `Orange35\ColorPickerElement\Data\Form\Element\Color` - element for system configuration

## Installation

    composer require orange35/magento2-color-picker-element

## Usage
### Add element in system configuration

    <field id="color" translate="label" type="Orange35\ColorPickerElement\Data\Form\Element\Color" sortOrder="10" showInDefault="1" showInWebsite="1" showInStore="1" canRestore="1">
        <label>Color</label>
    </field>

### Attach to input

**Example 1:**
    
    <script type="text/x-magento-init">
    {
        "color-element-id": {
            "orange35ColorPickerElement": {}
        }
    }
    </script>

**Example 2:**
    
    <input type="text" name="color" data-mage-init='{"orange35ColorPickerElement":{}}' />
