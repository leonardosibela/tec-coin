<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Buyer;
use App\User;
use App\Order;
use App\Deposit;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Support\Facades\Auth;
use App\ExtractRecord;
class BuyerController extends Controller
{
    public function products()
    {
        $storeProducts = Product::where('type_id', Product::$TYPE_STORE)->get();
        $xeroxProducts = Product::where('type_id', Product::$TYPE_XEROX)->get();
        $canteenProducts = Product::where('type_id', Product::$TYPE_CANTEEN)->get();

        return redirect("user.products")->with(
            ["storeProducts" => $storeProducts, "xeroxProducts" => $xeroxProducts, "canteenProducts" => $canteenProducts]
        );
    }

    public function balance()
    {
        // Pegar todos os valores que foram adicionados à conta do comprador (usuário logado)
        $loggedBuyer = Buyer::where("user_id", Auth::user()->id)->first();
        $deposits = Deposit::where("buyer_id", $loggedBuyer->id)->get();

        // Pegar todas as compras realizadas por esse usuário cujo status seja igual a (1) Ordered ou 2 (Accepted)
        $orders = Order::where(["buyer_id" => $loggedBuyer->id, "status_id" => 1, "status_id" => 2])->get();
        
        /* Mergear os dois arrays em um único array com o seguinte padrão:

            // Para uma compra:
            value: 5
            description:"Compra de Coxinha"
            date:14/11/2018
            type:saida

            // Para um recebimento:
            value: 30
            description:"Melhor nota da turma"
            date:14/11/2018
            type:entrada
        */
        $displayExtract = self::toExtract($orders, $deposits);

        // Pegar o saldo do usuário
        $balance = self::toBalance($orders, $deposits);
        
        return view("buyer.extract")->with(['balance' => $balance, 'displayExtract' => $displayExtract ]);
    }

    public function orderProduct($idbuyer , $idproduct , $idseller)
    {
        // Fazer um pedido pelo id do produto
        $value = Product::where('id' , $idproduct)->value('value')->get();
        $order = new Order;
        $order->product_id = $idproduct;
        $order->buyer_id = $idbuyer;
        $order->seller_id = $idseller;
        $order->status_id = 1;
        $order->value = $value;
        $order->save();
    }

    public function profile()
    {
        // Chamar a tela de edição de perfil
        return view('buyer.edit_profile');
    }

    public function updateProfile(Request $r, $id)
    {
        // Atualizar dados do perfil do usuário
        User::where('id', $id)->update(['name' => $r['name']]);
        User::where('id', $id)->update(['email' => $r['email']]);
        //Modificar também o tipo de usuário????
        /* User::where('id', $id)->update(['user_type' => $r['role']]); */
        // Remover e adicionar em sua respectiva tabela?
    }

    public function orders()
    {
        return redirect("buyer.orders");
    }

    private function toExtract($orders, $deposits) {

        foreach ($orders as $order) {   
            $extractArray[] = new ExtractRecord($order->value, $order->description(), $order->created_at, "order");
        }
        
        foreach ($deposits as $deposit) {
            $extractArray[] = new ExtractRecord($deposit->value, $deposit->description, $deposit->created_at, "deposit");
        }
        
        usort($extractArray, function($a, $b) {
            return strcmp($a->date, $b->date);
        });

        return $extractArray;
    }

    public function toBalance($orders, $deposits) {
        $balance = 0;
        foreach ($orders as $order) {
            $balance -= $order->value;
        }
        
        foreach ($deposits as $deposit) {
            $balance += $deposit->value;
        }

        return $balance;
    }
}
