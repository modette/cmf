# Forms

> Requires [core-ext-ui](../packages/core-ext-ui.md)

## Content

- TODO

## Structure

```
/your
    /module
        packages/
            (ui|admin|front)/
                forms/
                    Containers/ # TODO - mají smysl samostatné kontajnery? měly by mít vlastní složku, či templates a translations?
					ExampleForm/ # TODO
						Containers/
							ExampleContainer.php
						templates/
							TODO
						translations/
							TODO
						ExampleForm.php
						CreateExampleForm.php
						UpdateExampleForm.php
```

## Code example

TODO
	- https://www.vysinsky.cz/nette/nette-forms/
	- https://github.com/JanTvrdik/documentation/blob/master/pla.cs/dedicnost-vs-kompozice.txt
	- create form - save data
	- edit form - hidden id, load default values, update data

## What next?

Take a look at documentation of packages that were used to build this feature:

- [nette/forms](https://doc.nette.org/en/3.0/forms)
- [contributte/forms](https://github.com/contributte/forms/blob/master/.docs/README.md)
