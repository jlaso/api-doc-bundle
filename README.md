# api-doc-bundle

## Bundle to document your API

# Installation

composer require jlaso/api-doc-bundle

On AppKernel add:

```
    ...
    new JLaso\ApiDocBundle\JLasoApiDocBundle(),
    ...

```


# Configuration

Add this keys to your config.yml file

```
    jlaso_api_doc:
        title: "The title you want to have in the documentation page"
        output_folder: "The folder where the documentator will put the code"
        assets_folder: "The folder where the assets are ... example favicon.ico"  # optional
```

# Use

Launch the script to generate documentation

```
    app/console jlaso:api-doc
```



