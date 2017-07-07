<p class="info">
    <?php echo $info; ?>
</p>
<section>
    <?php if (count($data) > 0) : ?>
    <table>
        <thead>
            <th>#</th>
            <th><?php _e('Keyword', 'wp-search-statistics'); ?></th>
            <th class="hits"><?php _e('Logged in', 'wp-search-statistics') ?></th>
            <th class="hits"><?php _e('Results', 'wp-search-statistics'); ?></th>
        </thead>
        <tbody>
            <?php $i = 0; foreach ($data as $item) : $i++; ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $item->query; ?></td>
                <td class="hits"><?php if ($item->logged_in) echo '<span class="dashicons dashicons-yes"></span>'; ?></td>
                <td class="hits"><?php echo $item->results; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else : ?>
    <p><?php _e('Nothing to show', 'wp-search-statistics'); ?></p>
    <?php endif; ?>
</section>

