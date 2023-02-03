<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

use App\Models\CategoryModel;
use App\Models\BlogModel;
use PHPUnit\Util\Xml\Validator;

class ApiController extends ResourceController
{  
   private $db;

   function __construct() 
   {
      $this->db = db_connect();
   }

   function createCategory() {
      $rules = [
			"name" => "required|is_unique[categories.name]"
		];

		if(!$this->validate($rules)){
			
			$response = [
				"status" => 500,
				"message" => $this->validator->getErrors(),
				"error" => true,
				"data" => []
			];
		}else{
			
			$category_obj = new CategoryModel();

			$data = [
				"name" => $this->request->getVar("name"),
				"status" => $this->request->getVar("status")
			];

			if($category_obj->insert($data)){
				// data has been saved
				$response = [
					"status" => 200,
					"message" => "Category created successfully",
					"error" => false,
					"data" => []
				];
			}else{
				// failed to save data
				$response = [
					"status" => 500,
					"message" => "Failed to created category",
					"error" => true,
					"data" => []
				];
			}
		}

		return $this->respondCreated($response);
   }

   function listCategory() {

      $category_obj = new CategoryModel();

      $categories = $category_obj->findAll();

      $response = [
         "status" => 200,
         "message" => "Categories fetched sucessfully",
         "error" => false,
         "data" => $categories
      ];

      return $this->respondCreated($response);
   }

   function createBlog() {

      $category_obj = new CategoryModel();

      $rules = [
         "category_id" => "required",
         "title" => "required"
      ];

      if(!$this->validate($rules)) {

         $response = [
            "status" => 500,
            "message" => $this->validator->getErrors(),
            "error" => true,
            "data" => [],
         ];

      } else {

         // Pass the value of the category input as a parameter to search 
         // the categories table.
         $category_exists = $category_obj->find($this->request->getVar());

         if(!empty($category_exists)) {
            // Category exists

            $blog_obj = new BlogModel();

            $data = [
               "category_id" =>$this->request->getVar("category_id"),
               "title" => $this->request->getVar("title"),
               "content" => $this->request->getVar("content"),
            ];

            if($blog_obj->insert($data)) {
               	// blog created
					$response = [
						"status" => 200,
						"message" => "Blog has been created",
						"error" => false,
						"data" => []
					];
            } else {
               	// failed to create blog
					$response = [
						"status" => 500,
						"message" => "Failed to create blog",
						"error" => true,
						"data" => []
					];
            }

         } else {
            // Category does not exist

            $response = [
					"status" => 404,
					"message" => "Category not found",
					"error" => true,
					"data" => []
				];
         }

      }

      return $this->respondCreated($response);

   }

   function listBlogs() {
      $builder = $this->db->table("blogs as b");
      // b becomes an aliases for blogs

      $builder->select("b.*, c.name as category_name");
      // Select * From blogs, categories.name as category_name (an aliases)

      $builder->join("categories as c", "c.id = b.category_id");

      $data = $builder->get()->getResult();


      $response = [
         "status" => 200,
         "message" => "List of blogs",
         "error" => false,
         "data" => $data
      ];

      return $this->respondCreated($response);
   }

   function singleBlogDetail($blog_id) {
      $builder = $this->db->table('blogs as b');

      $builder->select("b.*, c.name as category_name");

      $builder->join("categories as c", "c.id = b.category_id");

      $builder->where("b.id", $blog_id);

      $data = $builder->get()->getRow();

      $response = [
         "status"=> 200,
         "message"=> "Single blog detail",
         "error" => false,
         "data" => $data
      ];

      return $this->respondCreated($response);
   }

   function updateBlog($blog_id) {

      $blog_obj = new BlogModel();

      // Check if blog exists

      $is_exists = $blog_obj->find($blog_id);

      // If blog exists, set rules and validate and insert

      if(!empty($is_exists)) {
         // validate
         $rules = [
            "category_id" => "required",
            "title" => "required"
         ];

         // if data is invalid return error response
         if(!$this->validate($rules)) {

            $response = [
					"status" => 500,
					"message" => $this->validator->getErrors(),
					"error" => true,
					"data" => []
				];
         } else {

            // If data is clean, proceed
            $category_obj = new CategoryModel();

            $is_cat_exists = $category_obj->find($this->request->getVar("category_id"));
            
            if(!empty($is_cat_exists)) {
               // Category exists, so collect data and store
               $data = [
                  "category_id" => $this->request->getVar("category_id"),
                  "title" => $this->request->getVar("title"),
                  "content" => $this->request->getVar("content"),
               ];

               $blog_obj->update($blog_id, $data);

               $response = [
						"status" => 200,
						"message" => "Blog updated successfully",
						"error" => false,
						"data" => []
					];

            } else {
               // Category does not exist, so return error repsonse
               $response = [
                  "status" => 404,
                  "message" => "Category not found!",
                  "error" => true,
                  "data" => []
               ];
            }
         }
      } else {
         	// blog does not exists
			$response = [
				"status" => 404,
				"message" => "Blog not found",
				"error" => true,
				"data" => []
			];
      }

      return $this->respondCreated($response);
   }

   function deleteBlog($blog_id) {
      $blog_obj = new BlogModel();

      $blog_is_exist = $blog_obj->find($blog_id);

      if(!empty($blog_is_exist)) {

         $blog_obj->delete($blog_id);

         $response = [
				"status" => 200,
				"message" => "Blog deleted successfully",
				"error" => false,
				"data" => []
			];

      }

      return $this->respondCreated($response);
   }
}
