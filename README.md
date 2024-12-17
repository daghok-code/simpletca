## Custom Shortcuts
To add your own custom shortcut follow the steps below:
1. Create class `*MyShortcut*Shortcut` (important: append the **Shortcut** after your custom name) in folder `Shortcut`with namespace `Febis\SimpleTca\Shortcut`
2. Extends custom shortcut class from `AbstractShortcut` (preferred, to have additional utilities) or at least make sure to implement `TcaShortcutInterface` in your custom shortcut class
3. Important for autocompletion: add annotation to `TcaGenerator`: `@method static TcaShortcutInterface create*MyShortcut*()`

### Implement Shortcut
```
namespace Febis\SimpleTca\Shortcut;

/**
 * @method self withProperty1($property1 = null) // make Properties available for autocompletion
 * @method self withProperty2($property2 = null)
 * ...
 */
class CustomShortcut extends AbstractShortcut
{
    protected static function getType(): string
    {
        return "input"; // TCA config type
    }

    protected static function getAllowedProperties(): array
    {
        return ['property1', 'property2']; // define Properties, allowed to override by with...-methods
    }

    protected static function getDefaultProperties(): array
    {
        return [
            'property1' => 'foo',
        ];
    }

    public function __construct(
        ?string $label = null, // label is always first argument
        protected ?string $property1 = null, // define here your properties for class
        protected ?string $property2 = null
    ) {
        parent::__construct($label);
    }
}
```


### Debug Outputs
You can set environment variable "SIMPLETCA_DEBUG=1" to enable debug outputs.

- adds fully generated tsconfig and typoscript to var/cache/code/simpletca_[tsconfig|typoscript]/....php
