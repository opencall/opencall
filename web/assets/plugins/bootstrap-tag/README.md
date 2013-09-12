# Tags for Twitter Bootstrap 2.x

bootstrap-tag is a javascript plugin that makes it easy to create simple, beautiful tag inputs with the help of [Twitter Bootstrap](http://twitter.github.com/bootstrap/).

## Usage

Just like in Bootstrap you can activate it without any JavaScript, just by adding a data-attribute, you can make it automatically work.

Simply add `data-provide="tag"`. You can set options via data-attributes, too.

    <input class="input-tag" type="text" name="tags" data-provide="tag">
    
Alternatively, you can initialize via JavaScript:

    $('.input-tag').tag(options);

### Options

* **caseInsensitive** _(optional)_ Whether or not search and matching should be case insensitive. Default to `true`.
* **allowDuplicates** _(optional)_ Whether or not to allow duplicate tags. Defaults to `false`.
* **source** _(optional)_ The data source to query against. May be an array of strings or a function. Defaults to `[]`.

## Example

You can try it [here](http://fdeschenes.github.com/bootstrap-tag/). I've also included the example in the `docs` folder.