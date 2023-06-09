<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ServiceRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ServiceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ServiceCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Service::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/service');
        CRUD::setEntityNameStrings('service', 'services');

        if (!backpack_user()->can('services.create')) {
            CRUD::denyAccess('create');
        }
        if (!backpack_user()->can('services.update')) {
            CRUD::denyAccess('update');
        }

        if (!backpack_user()->can('services.delete')) {
            CRUD::denyAccess('delete');
        }

        if (!backpack_user()->can('services.index')) {
            CRUD::denyAccess('list');
        }
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // CRUD::column('id');
        CRUD::column('name');
        CRUD::column('name_en');
        CRUD::column('duration');
        CRUD::column('stylist_price');
        CRUD::column('art_director_price');
        CRUD::column('hair_size');
        CRUD::column('hair_size_nl');
        CRUD::column('hair_type');
        CRUD::column('hair_type_nl');
        CRUD::column('category');
        CRUD::column('category_en');
        // CRUD::column('deleted_at');
        // CRUD::column('created_at');
        // CRUD::column('updated_at');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ServiceRequest::class);

        // CRUD::field('id');
        CRUD::field('name')->attributes(['required' => true]);
        CRUD::field('name_en')->attributes(['required' => true]);
        CRUD::field('duration')->attributes(['required' => true]);
        CRUD::field('stylist_price')->attributes(['required' => true]);
        CRUD::field('art_director_price')->attributes(['required' => true]);
        CRUD::field('hair_size')->type('select_from_array')->options(['Men' => 'Men', 'Ladies Short' => 'Ladies Short', 'Ladies Midlong' => 'Ladies Midlong', 'Ladies Long' => 'Ladies Long'])->attributes(['required' => true]);
        CRUD::field('hair_size_nl')->type('select_from_array')->options(['Heren' => 'Heren', 'Dames Kort' => 'Dames Kort', 'Dames Halflang' => 'Dames Halflang', 'Dames Lang' => 'Dames Lang']);
        CRUD::field('hair_type')->type('select_from_array')->options(['Straight' => 'Straight', 'Wavy' => 'Wavy', 'Curly' => 'Curly', 'Frizzy' => 'Frizzy']);
        CRUD::field('hair_type_nl')->type('select_from_array')->options(['Steil' => 'Steil', 'Golvend' => 'Golvend', 'Krullend' => 'Krullend', 'Kroes' => 'Kroes']);
        CRUD::field('category')->type('select_from_array')->options(['Wassen, knippen, drogen' => 'Wassen, knippen, drogen', 'Baard modeleren' => 'Baard modeleren', 'Kleuren' => 'Kleuren', 'Uitgroei' => 'Uitgroei', 'Baard kleuren' => 'Baard kleuren', 'Relaxen' => 'Relaxen'])->attributes(['required' => true]);
        CRUD::field('category_en')->type('select_from_array')->options(['Wash, cut, dry' => 'Wash, cut, dry', 'Beard modeling' => 'Beard modeling', 'Colors' => 'Colors', 'Outgrowth' => 'Outgrowth', 'Beard colors' => 'Beard colors', 'Relax' => 'Relax'])->attributes(['required' => true]);
        // CRUD::field('deleted_at');
        // CRUD::field('created_at');
        // CRUD::field('updated_at');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
