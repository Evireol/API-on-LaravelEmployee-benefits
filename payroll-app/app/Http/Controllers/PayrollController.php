<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{

    public function index()
    {
        // Найти все непогашенные транзакции
        $unpaidTransactions = Transaction::where('paid', false)->get();

        // Создать массив для хранения сумм выплат для каждого сотрудника
        $payouts = [];

        // Рассчитать суммы и выплатить сотрудникам
        foreach ($unpaidTransactions as $transaction) {
            $employeeId = $transaction->employee_id;
            $hours = $transaction->hours;

            // Рассчитать сумму выплаты для данной транзакции
            $paymentAmount = $hours * 1000;

            // Добавить сумму к выплате сотруднику
            if (isset($payouts[$employeeId])) {
                $payouts[$employeeId] += $paymentAmount;
            } else {
                $payouts[$employeeId] = $paymentAmount;
            }

            // Пометить транзакцию как погашенную
            $transaction->paid = true;
            $transaction->save();
        }

        // Вернуть результат в формате JSON
        return response()->json($payouts);
    }
    

    public function submitTransaction(Request $request, $employeeId)
    {
        $request->validate([
            'hours' => 'required|numeric|min:0',
        ]);
    
        $employee = Employee::findOrFail($employeeId); // Проверяем существование сотрудника
        $transaction = new Transaction();
        $transaction->employee_id = $employeeId;
        $transaction->hours = $request->input('hours');
        $transaction->save();
    
        return response()->json(['message' => 'Transaction submitted successfully'], 201);
    }    

    public function unpaidSalaries()
    {
        $unpaidSalaries = Employee::leftJoin('transactions', 'employees.id', '=', 'transactions.employee_id')
            ->selectRaw('employees.id, SUM(transactions.hours) AS total_hours')
            ->groupBy('employees.id')
            ->get();
    
        return response()->json($unpaidSalaries, 200);
    }
     
    public function payAllSalaries()
    {
        // Получите список всех сотрудников и суммы их накопившихся зарплат
        $employees = Employee::all();
    
        foreach ($employees as $employee) {
            // Получите сумму накопившихся часов работы для сотрудника
            $totalHours = Transaction::where('employee_id', $employee->id)->sum('hours');
    
            // Рассчитайте зарплату для сотрудника (здесь можно использовать вашу логику расчета)
            $salary = $totalHours * $hourlyRate; // Здесь $hourlyRate - ставка за час работы
    
            // Выплачиваем зарплату сотруднику (псевдокод)
            // Вам нужно реализовать метод выплаты, например, отправить деньги на счет сотрудника или иным способом
            $this->makePaymentToEmployee($employee, $salary);
    
            // Помечаем все транзакции сотрудника как погашенные
            Transaction::where('employee_id', $employee->id)->update(['paid' => true]);
        }
    
        return response()->json(['message' => 'All salaries paid successfully'], 200);
    }
    
    
}
?>