
<h1>Gestion de usuarios</h1>

<table class="tabla">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th class="txt-centro">Activado</th>
            <th>Alta</th>
            <th class="txt-centro">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($usuarios)): ?>
            <tr><td colspan="7" class="txt-centro">No hay usuarios.</td></tr>
        <?php endif; ?>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['nombre'] . ' ' . ($u['apellidos'] ?? '')) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['rol']) ?></td>
                <td class="txt-centro"><?= $u['activado'] ? 'Si' : 'No' ?></td>
                <td><?= htmlspecialchars($u['fecha_alta']) ?></td>
                <td class="txt-centro">
                    <a href="<?= URL_BASE ?>/admin/borrarUsuario/<?= $u['id'] ?>"
                       class="boton boton-borrar"
                       onclick="return confirm('¿Seguro que quieres eliminar a <?= htmlspecialchars(addslashes($u['nombre'])) ?>?')">
                        Eliminar
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p class="volver"><a href="<?= URL_BASE ?>/admin">&larr; Volver al panel</a></p>