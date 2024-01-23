<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\XmlController;
use App\Http\Controllers\ExportarController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\BackupController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [LoginController::class, 'index'])->name('index');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/esqueceuSenha', [LoginController::class, 'esqueceuSenha'])->name('esqueceuSenha');
Route::post('/recuperarSenha', [LoginController::class, 'recuperarSenha'])->name('recuperarSenha');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/teste', [XmlController::class, 'teste'])->name('teste');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/perfil', [DashboardController::class, 'perfil'])->name('perfil');
    Route::post('/perfilUpdate', [DashboardController::class, 'perfilUpdate'])->name('perfil.update');
    Route::get('/perfilAlterarSenha', [DashboardController::class, 'alterarSenha'])->name('perfil.alterarSenha');
    Route::post('/perfilAlterarSenhaUpdate', [DashboardController::class, 'alterarSenhaUpdate'])->name('perfil.alterarSenha.update');

    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios');
    Route::get('/usuariosAdicionar', [UsuarioController::class, 'adicionar'])->name('usuarios.adicionar');
    Route::post('/usuariosInsert', [UsuarioController::class, 'insert'])->name('usuarios.insert');
    Route::get('/usuariosEditar/{id}', [UsuarioController::class, 'editar'])->name('usuarios.editar');
    Route::post('/usuariosUpdate', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::get('/usuariosExcluir/{id}', [UsuarioController::class, 'excluir'])->name('usuarios.excluir');
    Route::post('/usuariosDelete', [UsuarioController::class, 'delete'])->name('usuarios.delete');
    Route::get('/usuariosVisualizar/{id}', [UsuarioController::class, 'visualizar'])->name('usuarios.visualizar');
    Route::get('/usuariosAlterarSenha/{id}', [UsuarioController::class, 'alterarSenha'])->name('usuarios.alterarSenha');
    Route::post('/usuariosAlterarSenhaUpdate', [UsuarioController::class, 'alterarSenhaUpdate'])->name('usuarios.alterarSenha.update');

    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes');
    Route::get('/clientes/adicionar', [ClienteController::class, 'adicionar'])->name('clientes.adicionar');
    Route::post('/clientes/insert', [ClienteController::class, 'insert'])->name('clientes.insert');
    Route::get('/clientes/editar/{id}', [ClienteController::class, 'editar'])->name('clientes.editar');
    Route::post('/clientes/update', [ClienteController::class, 'update'])->name('clientes.update');
    Route::get('/clientes/excluir/{id}', [ClienteController::class, 'excluir'])->name('clientes.excluir');
    Route::post('/clientes/delete', [ClienteController::class, 'delete'])->name('clientes.delete');
    Route::get('/clientes/contadores/{id}', [ClienteController::class, 'contadores'])->name('clientes.contadores');
    Route::post('/clientes/contadores/save', [ClienteController::class, 'contadoresSalvar'])->name('clientes.contadores.salvar');

    Route::get('/relXml', [XmlController::class, 'xml'])->name('xml');
    Route::post('/relXmlFiltrar', [XmlController::class, 'xmlFiltrar'])->name('xmlFiltrar');
    Route::get('/abrirXml/{id}', [XmlController::class, 'abrirXml'])->name('abrirXml');

    Route::get('/vendas', [XmlController::class, 'vendas'])->name('vendas');
    Route::post('/vendasFiltrar', [XmlController::class, 'vendasFiltrar'])->name('vendasFiltrar');


    Route::get('/arqXml/meses/{id}', [XmlController::class, 'meses'])->name('xml.meses');
    Route::get('/arqXml/listar/{id_cliente}/{mes_ano}', [XmlController::class, 'listar'])->name('xml.listar');



    Route::post('/exportar', [ExportarController::class, 'exportar'])->name('exportar');

    Route::get('/inventarios', [InventarioController::class, 'index'])->name('inventarios');
    Route::get('/inventariosListar/{id}', [InventarioController::class, 'listar'])->name('inventariosListar');

    Route::get('/backups', [BackupController::class, 'index'])->name('backups');
    Route::get('/backupsListar/{id}', [BackupController::class, 'listar'])->name('backupsListar');

});
