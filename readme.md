# Faster MySQL Data Fetch

Simple library which is used to database fetch - faster than typing whole query all the time.

### How to install

Import database class into your project.

### How to use

There are few methods to use.

At first you have to set work table.
```
$data = $e  ->setTable('tableName') //string

```

Now define what you want to do.

```

	->setQuery('select') // select || insert, ( update not implemented :( )

```

If you want specify columns to fetch use and WHERE statement 

```

   $e->setElements(['name','surname']) //array
     ->setWhere(['id>"3"'])
	 ->findMany(); // or findOne();

```

Format data to json/array/table and table row customize

```
	->setFormat('table') // json/array/table

	->setRowClass('tr-row') // set class to row

```

Append fetched data

```
$all_data = $data->fetch_data();

```

Insert new data (this must be used with setElements - specified columns name)

```

->setValues(['Name','Surname']) //array

```

## Why?

If you can not use Doctrine or other library, and this one is small and fast.

