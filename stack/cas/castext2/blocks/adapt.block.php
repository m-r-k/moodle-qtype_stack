<?php
// This file is part of Stateful
//
// Stateful is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stateful is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stateful.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This class adds in the "adapt" blocks to castext.
 * @package    qtype_stack
 * @copyright  2025 University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../block.interface.php');

/**
 * This class adds in the adapt blocks to castext.
 */
class stack_cas_castext2_adapt extends stack_cas_castext2_block {

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function compile($format, $options): ?MP_Node {

        $style = '';
        if (isset($this->params['hidden'])) {
            if ($this->params['hidden'] == 'true') {
                $style = 'style="display:none;"';
            }
        }

        $body = new MP_List([new MP_String('adapt')]);
        $body->items[] = new MP_String($this->params['id']);
        $body->items[] = new MP_String($style);

        foreach ($this->children as $item) {
            $c = $item->compile($format, $options);
            if ($c !== null) {
                $body->items[] = $c;
            }
        }

        return $body;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function is_flat(): bool {
        return false;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function postprocess(array $params, castext2_processor $processor,
        castext2_placeholder_holder $holder): string {
        $id = $params[1];
        $style = $params[2];
        // Use the input field naming to get the question usage level id.
        // Add some extra chars to avoid likely collisions with inputs, those cannot
        // have the `-`-char in their names.
        $adapt_id = $processor->qa->get_qt_field_name('-adapt_' . $id);
        
        $content = "";
        for ($i = 3; $i < count($params); $i++) {
            if (is_array($params[$i])) {
                $content .= $processor->process($params[$i][0], $params[$i], $holder, $processor);
            } else {
                $content .= $params[$i];
            }
        }
        
        $body = "<div id='".$adapt_id."' ".$style.">".$content."</div>";
        return $body;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate_extract_attributes(): array {
        $r = [];
        if (!isset($this->params['id'])) {
            return $r;
        }
        return $r;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate(&$errors=[], $options=[]): bool {
        if (!array_key_exists('id', $this->params)) {
            $errors[] = new $options['errclass']('Adapt block requires a id parameter.', $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }
        return true;
    }
}
