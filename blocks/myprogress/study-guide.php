tools/deploy_board_single_block_routes.sh:1143:    grep -Fq "<span class=\"subject-study-library__journey-step\">$step</span>" "$LIBRARY_TEMP"
tools/deploy_board_single_block_routes.sh:1145:main_study_guide_block="$(sed -n '/subject-study-library__journey-step">1</,/subject-study-library__journey-step">2</p' "$LIBRARY_TEMP")"
tools/deploy_board_single_block_routes.sh:1146:secondary_study_guides_block="$(sed -n '/subject-study-library__journey-step">2</,/subject-study-library__journey-step">3</p' "$LIBRARY_TEMP")"
tools/deploy_board_single_block_routes.sh:1147:secondary_guide_block="$(sed -n '/subject-study-library__journey-step">3</,/subject-study-library__journey-step">4</p' "$LIBRARY_TEMP")"
