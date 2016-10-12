<section>
    <?php if (count($data) > 0) : ?>
    <table>
        <thead>
            <th>#</th>
            <th><?php _e('Keyword', 'search-enhancer'); ?></th>
            <th class="hits"><?php _e('Results', 'search-enhancer'); ?></th>
        </thead>
        <tbody>
            <?php $i = 0; foreach ($data as $item) : $i++; ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $item->query; ?></td>
                <td class="hits"><?php echo $item->results; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else : ?>
    <p><?php _e('Nothing to show', 'search-enhancer'); ?></p>
    <?php endif; ?>
</section>

