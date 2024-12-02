<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Storage;

class MyModel extends Model
{
    protected $prefix = 'URUT';
    public $useAlias = false;

    public function uploadFile($fileName, $uploadPath, $allowedFile = ['jpg', 'png'])
    {
        // Pengecekan apakah path folder tersedia
        if (!Storage::exists($uploadPath)) {
            // Jika tidak tersedia, maka membuat direktori
            Storage::makeDirectory($uploadPath);

            if (!Storage::exists($uploadPath)) {
                return ['status' => false, 'message' => 'folder untuk upload tidak tersedia.'];
            }
        }

        // Pengecekan apakah folder writable
        if (!is_writable(Storage::path($uploadPath))) {
            return ['status' => false, 'message' => 'folder untuk upload tidak dapat dimodifikasi.'];
        }

        if (request()->hasFile($fileName) && request()->file($fileName)->isValid()) {
            $file = request()->file($fileName);

            // Periksa apakah tipe file diizinkan
            if (!empty($allowedFile) && !in_array($file->extension(), $allowedFile)) {
                return ['status' => false, 'message' => 'extensi file tidak didukung.'];
            }

            // Generate nama unik untuk file
            $uniqueFileName = uniqid() . '_' . time() . '.' . $file->extension();

            // Simpan file ke direktori yang ditentukan
            if ($file->storeAs($uploadPath, $uniqueFileName)) {
                return ['status' => true, 'message' => 'berhasil mengupload file.', 'data' => $uniqueFileName];
            }
        }
        return ['status' => false, 'message' => 'gagal mengupload file.'];
    }

    public function getAliases($column = null)
    {
        if ($column) {
            return $this->aliases[$column];
        }
        return $this->aliases;
    }

    public function getReal($column = null)
    {
        if ($column) {
            return array_search($column, $this->aliases);
        }
        $originalMapping = [];

        foreach ($this->aliases as $original => $alias) {
            $originalMapping[$alias] = $original;
        }

        return $originalMapping;
    }

    public function toAliases($otherClass = null, $includeNoAlias = false)
    {
        $aliasedResult = [];
        $aliased = $this->aliases;
        if ($otherClass) {
            foreach ($otherClass as $c) {
                $class = app($c);
                $otherAliases = $class->getAliases();
                $aliased = $aliased + $otherAliases;
            }
        }
        $real = $this->toArray();
        foreach ($real as $k => $v) {
            if (!is_array($v)) {
                if (isset($aliased[$k])) {
                    $aliasedResult[$aliased[$k]] = $v;
                } else {
                    if ($includeNoAlias) {
                        $aliasedResult[$k] = $v;
                    }
                }
            } else {
                foreach ($v as $vk => $vv) {
                    if (!is_array($vv)) {
                        if (isset($aliased[$vk])) {
                            $aliasedResult[$k][$aliased[$vk]] = $vv;
                        }
                        if ($includeNoAlias) {
                            $aliasedResult[$k] = $v;
                        }
                    } else {
                        foreach ($vv as $vvk => $vvv) {
                            if (isset($aliased[$vvk])) {
                                $aliasedResult[$k][$vk][$aliased[$vvk]] = $vvv;
                            }
                            if ($includeNoAlias) {
                                $aliasedResult[$k] = $v;
                            }
                        }
                    }
                }
            }
        }
        return (object) $aliasedResult;
    }

    public static function fromAliases($aliasedData = null)
    {
        $aliasedData = ($aliasedData == null ? (new static)->attributes : $aliasedData);

        $originalData = [];

        foreach ($aliasedData as $aliasedKey => $value) {
            $originalKey = array_search($aliasedKey, (new static)->aliases);
            if ($originalKey) {
                $originalData[$originalKey] = $value;
            } else {
                $originalData[$aliasedKey] = $value;
            }
        }

        return new static($originalData);
    }

    public function defaultColumns($model = null)
    {
        $all = $this->aliases;
        if ($model != null) {
            $all = app($model)->getAliases();
        }
        $defaultAttributes = [];
        foreach ($all as $attribute => $v) {
            $defaultAttributes[$attribute] = null;
        }
        return $defaultAttributes;
    }

    public static function mapTo($data, $tables = [])
    {
        if ($tables == []) {

            $result = [];

            $fillableProperties = (new static)->fillable;

            $filteredData = array_intersect_key($data, array_flip($fillableProperties));

            $result = $filteredData;

            return $result;
        }
        $result = [];

        foreach ($tables as $table) {
            $fillableProperties = app($table)->fillable;

            $filteredData = array_intersect_key($data, array_flip($fillableProperties));

            $result[$table] = $filteredData;
        }

        return $result;
    }

    //primary query function
    public function createTransId($prefix = null)
    {
        if (!$prefix) {
            $prefix = $this->prefix;
        }
        $last = self::latest($this->primaryKey)->first();
        if (!$last || $last[$this->primaryKey] == '') {
            $last[$this->primaryKey] = 'A-00000000-0000001'; //sample
        }
        $exp = explode('-', $last[$this->primaryKey]);
        if (count($exp) < 3) {
            $exp[2] = '0000000';
        }
        $len = strlen($exp[2]);
        $next = ((int) $exp[2]) + 1;
        $digit = str_pad($next, $len, 0, STR_PAD_LEFT);
        return $prefix . '-' . date('Ymd') . '-' . $digit;
    }

    public function createId_($prefix = null)
    {
        if (!$prefix) {
            $prefix = $this->prefix;
        }
        $uniq = explode(' ', microtime())[1];
        return $prefix . '-' . substr($uniq, 4, 3) . '-' . substr($uniq, 0, 4) . '-' . substr($uniq, 7, 3);
    }

    //CRUD QUERY SECTION
    public function insertOrUpdate_($data = null, $isTrans = false, $createIdFunc = null)
    {
        if ($data == null) {
            $data = $this->attributes;
        }
        if (isset($data[$this->primaryKey])) {
            $find = self::find($data[$this->primaryKey]);
            if ($find) {
                $find->update($data);
                return $find;
            } else {
                //ketika terdapat id pada data. namun tidak ditemukan di database. artinya id di set manual
                return self::create($data);
            }
        }
        if ($isTrans) {
            $createIdMethod = 'createTransId';
        }
        if ($createIdFunc) {
            $createIdMethod = is_callable([$this, $createIdFunc]) ? $createIdFunc : 'createId';
        }
        $data[$this->primaryKey] = $this->$createIdMethod($this->prefix);

        return self::create($data);
    }

    public function insertOrUpdateBatch($dtArray = null, $uniq = null)
    { //No return
        if (!$uniq) {
            $uniq = $this->primaryKey;
        }
        if (!is_array($uniq)) {
            $uniq = [$uniq];
        }
        if ($dtArray) {
            return $this->upsert(
                $dtArray,
                $uniq
            );
        }
    }

    public function getRows($params = null, $result_array = false)
    {
        $datatable1 = (
            isset($params['draw']) &&
            isset($params['start']) &&
            isset($params['length'])
        );
        if ($datatable1) {
            return $this->getDatatable($params, $result_array);
        }

        $query = $this->newQuery();

        if (isset($params)) {
            if (isset($params['from'])) {
                $query = DB::table($params['from']);
            }

            $withModels = null; //untuk with nanti
            // preQuery
            if (isset($params['preQuery'])) {
                $preQuery = $params['preQuery'];
                $query = $preQuery($query);
            }

            //select
            if (isset($params['select'])) {
                $select = $params['select'];
                if (is_array($select)) {
                    $query->select($select);
                } elseif (is_string($select)) {
                    $query->select(DB::raw($select));
                } else {

                    return [
                        'status' => false,
                        'message' => 'Format Select tidak dikenali',
                        'code' => 400
                    ];
                }
            }


            // join
            if (isset($params['join'])) {
                $join = $params['join'];
                foreach ($join as $item) {
                    $joinType = $item['param'] == 'left' ? 'leftJoin' : ($item['param'] == 'right' ? 'rightJoin' : 'join');
                    $query->$joinType($item['table'], function ($join) use ($item) {
                        if (is_callable($item['on'])) {
                            $item['on']($join);
                        } elseif (is_array($item['on'])) {
                            if (count($item['on']) == 2) {
                                $join->on($item['on'][0], $item['on'][1]);
                            } elseif (count($item['on']) == 3) {
                                $join->on($item['on'][0], $item['on'][1], $item['on'][2]);
                            }
                        } else {
                            $join->on($item['on']);
                        }
                    });
                    if ($this->useAlias && isset($item['model']))
                        $withModels[] = $item['model'];
                }
            }

            // where
            if (isset($params['where'])) {
                if ($this->useAlias) {
                    $originalColumn = $this->getReal();
                } else {
                    $originalColumn = $this->fillable;
                }
                $where = $params['where'];
                if ($where != []) {
                    if (is_string($where)) {
                        $query->whereRaw($where);
                    } else if (is_array($where)) {
                        if (array_key_first($where) === 0) {
                            for ($i = 0; $i < count($where); $i++) {
                                $condition = $where[$i];
                                if (is_string($condition)) {
                                    //jika string
                                    $query->whereRaw($condition);
                                } elseif (is_array($condition)) {
                                    //jika array
                                    if (array_key_first($condition) !== 0) {
                                        //[ 'key' => 'value']
                                        if (count($condition) > 1) {
                                            foreach ($condition as $column => $value) {
                                                if ($this->useAlias) {
                                                    $column = $originalColumn[$column];
                                                }
                                                $query->where($column, $value);
                                            }
                                        } else {
                                            $column = key($condition);
                                            if ($this->useAlias) {
                                                $column = $originalColumn[$column];
                                            }
                                            $value = current($condition);
                                            $query->where($column, $value);
                                        }
                                    } else {
                                        if (count($condition) === 2) {
                                            // format ['column', 'value']
                                            $column = $condition[0];
                                            if ($this->useAlias) {
                                                $column = $originalColumn[$column];
                                            }
                                            $value = $condition[1];
                                            $query->where($column, '=', $value);
                                        } elseif (count($condition) === 3) {
                                            // format ['column', 'operator', 'value']
                                            list($column, $operator, $value) = $condition;
                                            if ($this->useAlias) {
                                                $column = $originalColumn[$column];
                                            }
                                            $query->where($column, $operator, $value);
                                        } else {
                                            return [
                                                'status' => false,
                                                'message' => 'Format where tidak dikenali',
                                                'code' => 400
                                            ];
                                        }
                                    }
                                } else {
                                    return [
                                        'status' => false,
                                        'message' => 'Format where tidak dikenali',
                                        'code' => 400
                                    ];
                                }
                            }
                        } else {
                            $condition = $where;
                            if (count($condition) > 1) {
                                foreach ($condition as $column => $value) {
                                    if ($this->useAlias) {
                                        $column = $originalColumn[$column];
                                    }
                                    $query->where($column, $value);
                                }
                            } else {
                                $column = key($condition);
                                if ($this->useAlias) {
                                    $column = $originalColumn[$column];
                                }
                                $value = current($condition);
                                $query->where($column, $value);
                            }
                        }
                    }
                }
            }


            // orWhere
            // if (isset($params['orWhere'])) {
            //     $orWhere = $params['orWhere'];
            //     $query->where(function ($query) use ($orWhere) {
            //         foreach ($orWhere as $k => $o) {
            //             if (is_array($o)) {
            //                 foreach ($o as $oo) {
            //                     $query->orWhere($this->getReal($k), '=', $oo);
            //                 }
            //             } else {
            //                 $query->orWhere($this->getReal($k), '=', $o);
            //             }
            //         }
            //     });
            // }

            // q => key to search
            if (isset($params['q'])) {
                $search = $params['q']['searchAble'];
                $value = $params['q']['value'];
                if (!$search || !$value) {
                    return [
                        'status' => false,
                        'message' => 'Format search query (q="kata yang dicari") tidak sesuai',
                        'code' => 400
                    ];
                }
            }

            // order
            if (isset($params['orderBy'])) {
                $order = $params['orderBy'];
                $query->orderByRaw($order);
            }

            // group
            if (isset($params['groupBy'])) {
                $group = $params['groupBy'];
                $query->groupBy($group);
            }
        }

        // with
        if (isset($params['with'])) {
            $with = $params['with'];
            $withModels = [];
            foreach ($with as $item) {
                if (is_array($item) && array_key_exists('model', $item)) {
                    $withModels[] = $item['model'];
                    $query->with($item['params']);
                } else {
                    $withModels[] = $item;
                    $query->with($item);
                }
            }
        }

        // praQuery
        if (isset($params['praQuery'])) {
            $praQuery = $params['praQuery'];
            $query = $praQuery($query);
        }

        if (isset($params['limit'])) {
            $query->limit($params['limit']);
        }

        if (isset($params['offset'])) {
            $query->offset($params['offset']);
        }

        $data = $query->get();

        // use alias or not
        if ($this->useAlias) {
            $includeNoAlias = $params['includeNoAlias'] || null;
            $data = $data->map(function ($d) use ($withModels, $includeNoAlias) {
                return $d->toAliases($withModels, $includeNoAlias);
            });
        }

        return [
            'status' => true,
            'message' => 'Berhasil Mengambil Data!',
            'data' => $data,
            'code' => 200,
        ];
    }

    public function getRow($params = null)
    {
        $query = $this->newQuery();

        if (isset($params)) {
            $withModels = null; //untuk with nanti
            // preQuery
            if (isset($params['preQuery'])) {
                $preQuery = $params['preQuery'];
                $query = $preQuery($query);
            }

            //select
            if (isset($params['select'])) {
                $select = $params['select'];
                if (is_array($select)) {
                    $query->select($select);
                } elseif (is_string($select)) {
                    $query->select(DB::raw($select));
                } else {

                    return [
                        'status' => false,
                        'message' => 'Format Select tidak dikenali',
                        'code' => 400
                    ];
                }
            }

            // join
            if (isset($params['join'])) {
                $join = $params['join'];
                foreach ($join as $item) {
                    $joinType = $item['param'] == 'left' ? 'leftJoin' : ($item['param'] == 'right' ? 'rightJoin' : 'join');
                    $query->$joinType($item['table'], function ($join) use ($item) {
                        if (is_callable($item['on'])) {
                            $item['on']($join);
                        } elseif (is_array($item['on'])) {
                            if (count($item['on']) == 2) {
                                $join->on($item['on'][0], $item['on'][1]);
                            } elseif (count($item['on']) == 3) {
                                $join->on($item['on'][0], $item['on'][1], $item['on'][2]);
                            }
                        } else {
                            $join->on($item['on']);
                        }
                    });
                    if ($this->useAlias && isset($item['model']))
                        $withModels[] = $item['model'];
                }
            }

            // where
            if (isset($params['where'])) {
                if ($this->useAlias) {
                    $originalColumn = $this->getReal();
                } else {
                    $originalColumn = $this->fillable;
                }
                $where = $params['where'];
                if ($where != []) {
                    if (is_string($where)) {
                        $query->whereRaw($where);
                    } else if (is_array($where)) {
                        if (array_key_first($where) === 0) {
                            for ($i = 0; $i < count($where); $i++) {
                                $condition = $where[$i];
                                if (is_string($condition)) {
                                    //jika string
                                    $query->whereRaw($condition);
                                } elseif (is_array($condition)) {
                                    //jika array
                                    if (array_key_first($condition) !== 0) {
                                        //[ 'key' => 'value']
                                        if (count($condition) > 1) {
                                            foreach ($condition as $column => $value) {
                                                if ($this->useAlias) {
                                                    $column = $originalColumn[$column];
                                                }
                                                $query->where($column, $value);
                                            }
                                        } else {
                                            $column = key($condition);
                                            if ($this->useAlias) {
                                                $column = $originalColumn[$column];
                                            }
                                            $value = current($condition);
                                            $query->where($column, $value);
                                        }
                                    } else {
                                        if (count($condition) === 2) {
                                            // format ['column', 'value']
                                            $column = $condition[0];
                                            if ($this->useAlias) {
                                                $column = $originalColumn[$column];
                                            }
                                            $value = $condition[1];
                                            $query->where($column, '=', $value);
                                        } elseif (count($condition) === 3) {
                                            // format ['column', 'operator', 'value']
                                            list($column, $operator, $value) = $condition;
                                            if ($this->useAlias) {
                                                $column = $originalColumn[$column];
                                            }
                                            $query->where($column, $operator, $value);
                                        } else {
                                            return [
                                                'status' => false,
                                                'message' => 'Format where tidak dikenali',
                                                'code' => 400
                                            ];
                                        }
                                    }
                                } else {
                                    return [
                                        'status' => false,
                                        'message' => 'Format where tidak dikenali',
                                        'code' => 400
                                    ];
                                }
                            }
                        } else {
                            $condition = $where;
                            if (count($condition) > 1) {
                                foreach ($condition as $column => $value) {
                                    if ($this->useAlias) {
                                        $column = $originalColumn[$column];
                                    }
                                    $query->where($column, $value);
                                }
                            } else {
                                $column = key($condition);
                                if ($this->useAlias) {
                                    $column = $originalColumn[$column];
                                }
                                $value = current($condition);
                                $query->where($column, $value);
                            }
                        }
                    }
                }
            }

            // // orWhere
            // if (isset($params['orWhere'])) {
            //     $orWhere = $params['orWhere'];
            //     $query->where(function ($query) use ($orWhere) {
            //         foreach ($orWhere as $k => $o) {
            //             if (is_array($o)) {
            //                 foreach ($o as $oo) {
            //                     $query->orWhere($this->getReal($k), '=', $oo);
            //                 }
            //             } else {
            //                 $query->orWhere($this->getReal($k), '=', $o);
            //             }
            //         }
            //     });
            // }

            // q => key to search
            if (isset($params['q'])) {
                $search = $params['q']['searchAble'];
                $value = $params['q']['value'];
                if (!$search || !$value) {
                    return [
                        'status' => false,
                        'message' => 'Format search query (q="kata yang dicari") tidak sesuai',
                        'code' => 400
                    ];
                }
            }

            // order
            if (isset($params['orderBy'])) {
                $order = $params['orderBy'];
                $query->orderByRaw($order);
            }
        }

        // with
        if (isset($params['with'])) {
            $with = $params['with'];
            $withModels = [];
            foreach ($with as $key => $item) {
                if (array_key_exists('model', $item)) {
                    $withModels[] = $item['model'];
                    $query->with($item['params']);
                } else {
                    $withModels[] = $item;
                    $query->with($key);
                }
            }
        }

        // praQuery
        if (isset($params['praQuery'])) {
            $praQuery = $params['praQuery'];
            $query = $praQuery($query);
        }

        $data = $query->first();

        // use alias or not
        if ($this->useAlias) {
            $includeNoAlias = $params['includeNoAlias'] || null;
            $data = $data->map(function ($d) use ($withModels, $includeNoAlias) {
                return $d->toAliases($withModels, $includeNoAlias);
            });
        }

        return [
            'status' => true,
            'message' => 'Berhasil Mengambil Data!',
            'data' => $data
        ];
    }

    public function getDatatable($params = null, $result_array = false)
    {
        $query = $this->newQuery();

        if (isset($params)) {
            if (isset($params['from'])) {
                $query = DB::table($params['from']);
            }

            $withModels = null; //untuk with nanti
            // preQuery
            if (isset($params['preQuery'])) {
                $preQuery = $params['preQuery'];
                $query = $preQuery($query);
            }

            //select
            if (isset($params['select'])) {
                $select = $params['select'];
                if (is_array($select)) {
                    $query->select($select);
                } elseif (is_string($select)) {
                    $query->select(DB::raw($select));
                } else {

                    return [
                        'status' => false,
                        'message' => 'Format Select tidak dikenali',
                        'code' => 400
                    ];
                }
            }


            // join
            if (isset($params['join'])) {
                $join = $params['join'];
                foreach ($join as $item) {
                    $joinType = $item['param'] == 'left' ? 'leftJoin' : ($item['param'] == 'right' ? 'rightJoin' : 'join');
                    $query->$joinType($item['table'], function ($join) use ($item) {
                        if (is_callable($item['on'])) {
                            $item['on']($join);
                        } elseif (is_array($item['on'])) {
                            if (count($item['on']) == 2) {
                                $join->on($item['on'][0], $item['on'][1]);
                            } elseif (count($item['on']) == 3) {
                                $join->on($item['on'][0], $item['on'][1], $item['on'][2]);
                            }
                        } else {
                            $join->on($item['on']);
                        }
                    });
                    if ($this->useAlias && isset($item['model']))
                        $withModels[] = $item['model'];
                }
            }

            // where
            if (isset($params['where'])) {
                if ($this->useAlias) {
                    $originalColumn = $this->getReal();
                } else {
                    $originalColumn = $this->fillable;
                }
                $where = $params['where'];
                if ($where != []) {
                    if (is_string($where)) {
                        $query->whereRaw($where);
                    } else if (is_array($where)) {
                        if (array_key_first($where) === 0) {
                            for ($i = 0; $i < count($where); $i++) {
                                $condition = $where[$i];
                                if (is_string($condition)) {
                                    //jika string
                                    $query->whereRaw($condition);
                                } elseif (is_array($condition)) {
                                    //jika array
                                    if (array_key_first($condition) !== 0) {
                                        //[ 'key' => 'value']
                                        if (count($condition) > 1) {
                                            foreach ($condition as $column => $value) {
                                                if ($this->useAlias) {
                                                    $column = $originalColumn[$column];
                                                }
                                                $query->where($column, $value);
                                            }
                                        } else {
                                            $column = key($condition);
                                            if ($this->useAlias) {
                                                $column = $originalColumn[$column];
                                            }
                                            $value = current($condition);
                                            $query->where($column, $value);
                                        }
                                    } else {
                                        if (count($condition) === 2) {
                                            // format ['column', 'value']
                                            $column = $condition[0];
                                            if ($this->useAlias) {
                                                $column = $originalColumn[$column];
                                            }
                                            $value = $condition[1];
                                            $query->where($column, '=', $value);
                                        } elseif (count($condition) === 3) {
                                            // format ['column', 'operator', 'value']
                                            list($column, $operator, $value) = $condition;
                                            if ($this->useAlias) {
                                                $column = $originalColumn[$column];
                                            }
                                            $query->where($column, $operator, $value);
                                        } else {
                                            return [
                                                'status' => false,
                                                'message' => 'Format where tidak dikenali',
                                                'code' => 400
                                            ];
                                        }
                                    }
                                } else {
                                    return [
                                        'status' => false,
                                        'message' => 'Format where tidak dikenali',
                                        'code' => 400
                                    ];
                                }
                            }
                        } else {
                            $condition = $where;
                            if (count($condition) > 1) {
                                foreach ($condition as $column => $value) {
                                    if ($this->useAlias) {
                                        $column = $originalColumn[$column];
                                    }
                                    $query->where($column, $value);
                                }
                            } else {
                                $column = key($condition);
                                if ($this->useAlias) {
                                    $column = $originalColumn[$column];
                                }
                                $value = current($condition);
                                $query->where($column, $value);
                            }
                        }
                    }
                }
            }


            // group
            if (isset($params['groupBy'])) {
                $group = $params['groupBy'];
                $query->groupBy($group);
            }
        }

        if (isset($params['order_by'])) {
            $order = $params['order_by'];
            if (is_array($params['order_by'])) {
                $query->orderBy($order[0], $order[1]);
            } else {
                $query->orderByRaw($order);
            }
        }

        // praQuery
        if (isset($params['praQuery'])) {
            $praQuery = $params['praQuery'];
            $query = $praQuery($query);
        }

        $datatable = Datatables::of($query)->addIndexColumn();
        // dattable action button
        if (isset($params['action'])) {
            $datatable
                ->addColumn('action', $params['action']); //function(row)
        }
        // order
        if (isset($params['orderBy'])) {
            $order = $params['orderBy'];
            if (strpos($order, ' ') !== FALSE) {
                $order = explode(' ', $order);
                $datatable->orderColumn(0, $order[0], $order[1]);
            } else {
                $datatable->orderColumn(0, $order);
            }
        }

        if (isset($params['pre_datatable'])) {
            $params['build_datatable'] = $params['pre_datatable'];
        }

        if (isset($params['build_datatable'])) {
            $build_action_datatable = $params['build_datatable'];
            $datatable = $build_action_datatable($datatable);
        }

        try {
            if ($result_array == true) {
                return $datatable->make(false)->original;
            }
            return $datatable->make(true);
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        }
    }

    public function insertOrUpdate($primaryKeyColumns = [], $values = [])
    {
        $primaryValues = [];
        foreach ($primaryKeyColumns as $column) {
            $primaryValues[$column] = $values[$column];
            unset($values[$column]);
        }

        return $this->updateOrInsert($primaryValues, $values);
    }

    public function createId($prefix, $primaryKey = '', $table = '')
    {
        $kode = 1;
        $data = DB::table($table)->select($primaryKey . " AS kode")->orderByRaw("RIGHT(" . $primaryKey . ", 7) DESC")->first();
        if ($data) {
            $kode = substr($data->kode, -7) + 1;
        }
        $bikin_kode = str_pad($kode, 7, "0", STR_PAD_LEFT);
        $kode_jadi = $prefix . "-" . $bikin_kode;
        return $kode_jadi;
    }

    public function insertData($data = null)
    {
        if ($data == null) {
            $data = $this->attributes;
        }
        $data_cleaned = Purify::clean($data);
        return self::create($data_cleaned);
    }

    public function updateData($data = null, $wheredata)
    {
        if ($data == null) {
            $data = $this->attributes;
        }
        $data_cleaned = Purify::clean($data);
        return self::where($wheredata)->update($data_cleaned);
    }

    public function countData($wheredata)
    {
        return self::where($wheredata)->count();
    }
}
