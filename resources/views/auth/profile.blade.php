<x-layouts.main-layout pageTitle="perfil de usuario">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-6">

                <p class="display-6 text-center mb-4">DEFINIR NOVA SENHA</p>

                <form action="{{ route('charge_password') }}" method="post">
                    @csrf

                    <div class="mb-3">
                        <label for="current_password" class="form-label">A sua senha atual</label>
                        <input
                            type="password"
                            class="form-control"
                            id="current_password"
                            name="current_password"
                        >
                        @error('current_password')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">Defina a nova senha</label>
                        <input
                            type="password"
                            class="form-control"
                            id="new_password"
                            name="new_password"
                        >
                        @error('new_password')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">
                            Confirmar a nova senha
                        </label>
                        <input
                            type="password"
                            class="form-control"
                            id="new_password_confirmation"
                            name="new_password_confirmation"
                        >
                        @error('new_password_confirmation')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-secondary px-5">
                            ALTERAR SENHA
                        </button>
                    </div>
                </form>

                @if (session('server_error'))
                    <div class="alert alert-danger text-center mt-3">
                        {{ session('server_error') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success text-center mt-3">
                        {{ session('success') }}
                    </div>
                @endif

                <hr class="my-5">

                <form action="{{ route('apagarConta') }}" method="post">
                    @csrf

                    <div class="mb-3">
                        <label for="texto" class="form-label">
                            Caso queira excluir conta, escreva "apagar" abaixo
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="texto"
                            name="texto"
                        >
                        @error('texto')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-danger px-5">
                            EXCLUIR CONTA
                        </button>
                    </div>

                    @if (session('server_message'))
                        <div class="alert alert-danger text-center mt-3">
                            {{ session('server_message') }}
                        </div>
                    @endif
                </form>

            </div>
        </div>
    </div>
</x-layouts.main-layout>
