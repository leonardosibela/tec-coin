<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\Order;
use App\Seller;
use App\Buyer;
use App\OrderStatus;
use App\Product;

class SellerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function products($id)
    {
        // Buscar todos os produtos do tipo de vendedor logado
        // Comment: Não tem nada ligando os produtos com o vendendo
        // e chamar a view de listagem de produtos (passandos esses produtos)
        return view('listproduct')->with('listproducts');
    }

    public function editProduct()
    {
        //Chamar a view de produtos
        return view ('editproduct');
    }

    public function updateProduct(Request $r , $id)
    {
        // Atualizar dados de um produto
        Product::where('id', $id)->update(['name' => $r['name']]);
        Product::where('id', $id)->update(['value' => $r['value']]);
        Product::where('id', $id)->update(['description' => $r['description']]);
        Product::where('id', $id)->update(['image' => $r['image']]);
        //Alterar o tipo de Produto?
        Product::where('id', $id)->update(['type_id' => $r['type']]);
        return view('home');
    }

    public function destroyProduct($id)
    {
        // Excluir produto
        Product::where('id', $id)->delete();
        return view('home');
    }

    public function createProduct()
    {
        // Devolver form de cadastro de produtos
            return view('seller.productregister');
    }

    public function storeProduct(Request $r)
    {
        // Receber dados do form de cadastro de produtos
        $product = new Product;
        $product->name = $r['name'];
        $product->value = $r['value'];
        $product->description = $r=['description'];
        $tipo = $r['type_id'];
        echo $tipo;
        die();
        $product->type_id = $r['type_id'];
        //Criar uma classe para validar a extensão da imagem?
        $product->image = 'example.png';
        $product->save();
        return view('home');
    }

    public function pendingOrders($id)
    {
        // Buscar todos os pedidos pendentes do tipo do vendedor logado
        $idseller = User::where('id', $id)->value('id');
        $all_idproduct_in_order = Order::where('idseller', $idseller)->value('product_id')->get();
        $all_order = Order::where('idseller', $idselle)->get();
        //Não sei se isso vai funcionar...
        $all_products = Product::whereIn('id', $all_idproduct_in_order)->get();
        // Exibir a tela de listagem de pedidos pendentes
        return view('listorder')->compact('all_order','all_products');
    }

    public function acceptOrder($id)
    {
        // Aprovar um pedido
        Order::where('seller_id',$id)->update(['status_id' => 2]);
        return view('home');
    }

    public function denyOrder($id)
    {
        // Recusar um pedido
        Order::where('seller_id',$id)->update(['status_id' => 3]);
    }
}
