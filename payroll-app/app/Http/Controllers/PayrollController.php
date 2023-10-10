<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{

        public function createEmployee(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:employees',
            'password' => 'required|min:6',
        ]);

        $employee = new Employee();
        $employee->email = $request->input('email');
        $employee->password = Hash::make($request->input('password'));
        $employee->save();

        return response()->json(['message' => 'Employee created successfully'], 201);
    }

    public function index()
    {
        // Найти все непогашенные транзакции
        $unpaidTransactions = Transaction::where('paid', false)->get();

        $payouts = [];

        // Рассчитать суммы и выплатит сотрудникам
        foreach ($unpaidTransactions as $transaction) {
            $employeeId = $transaction->employee_id;
            $hours = $transaction->hours;

            // Рассчитать сумму выплат для данной транзакции
            $paymentAmount = $hours * 1000;

            if (isset($payouts[$employeeId])) {
                $payouts[$employeeId] += $paymentAmount;
            } else {
                $payouts[$employeeId] = $paymentAmount;
            }

            // Пометит транзакцию как погашенную
            $transaction->paid = true;
            $transaction->save();
        }

        return response()->json($payouts);
    }
    

    public function submitTransaction(Request $request, $employeeId)
    {
        $request->validate([
            'hours' => 'required|numeric|min:0',
        ]);
    
        $employee = Employee::findOrFail($employeeId);
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

        $employees = Employee::all();
    
        foreach ($employees as $employee) {

            $totalHours = Transaction::where('employee_id', $employee->id)->sum('hours');

            $salary = $totalHours * $hourlyRate;

            $this->makePaymentToEmployee($employee, $salary);
    
            Transaction::where('employee_id', $employee->id)->update(['paid' => true]);
        }
    
        return response()->json(['message' => 'All salaries paid successfully'], 200);
    }
    
    
}
?>