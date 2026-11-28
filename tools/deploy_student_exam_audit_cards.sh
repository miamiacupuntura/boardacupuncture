#!/usr/bin/env bash
set -euo pipefail
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET="$MOODLE_ROOT/blocks/menu/herbology-board.php"
HELPER="$MOODLE_ROOT/blocks/myprogress/exam-audit-student-cards.php"
for tool in python3 php cp stat install mktemp date; do
    command -v "$tool" >/dev/null || { echo "ERROR: falta $tool" >&2; exit 1; }
done
TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
python3 - "$TARGET" "$TEMP_DIR/page.php" "$TEMP_DIR/helper.php" <<'PYTHON'
from pathlib import Path
import sys,re,base64
source=Path(sys.argv[1]).read_text()
marker='// Student exam audit cards v1'
if marker in source:raise SystemExit('Las tarjetas de auditoría ya están instaladas. No se cambió nada.')
if '$studentexamquestions = array_fill_keys' not in source:raise SystemExit('ERROR: no se encontró el inventario de preguntas de los exámenes disponibles. No se cambió nada.')
pattern=r'(<nav class="herb-board__simulation-exam-switch"[^>]*>.*?</nav>)'
matches=list(re.finditer(pattern,source,re.S))
if len(matches)!=2:raise SystemExit('ERROR: se esperaban dos selectores de examen. No se cambió nada.')
def insert(m):
    block="""
                <?php
                // Student exam audit cards v1
                if (!$mixdomains && in_array($track, ['simulation', 'exams'], true)) {
                    require_once(__DIR__ . '/../myprogress/exam-audit-student-cards.php');
                    block_myprogress_render_exam_audit_cards($studentexamquestions ?? [], $ises, $track,
                        $languagecontext, $track === 'simulation' ? $simulationexamnumber : $practiceexamnumber);
                }
                ?>
"""
    return m[0]+block
source=re.sub(pattern,insert,source,flags=re.S)
Path(sys.argv[2]).write_text(source)
Path(sys.argv[3]).write_bytes(base64.b64decode('PD9waHAKLy8gUHVibGljIGFnZ3JlZ2F0ZSBtZXRyaWNzIG9ubHk7IG5ldmVyIHJlbmRlciBxdWVzdGlvbnMsIGtleXMsIG9yIGRyYWZ0IHByb2plY3RzLgpkZWZpbmVkKCdNT09ETEVfSU5URVJOQUwnKSB8fCBkaWUoKTsKCmZ1bmN0aW9uIGJsb2NrX215cHJvZ3Jlc3NfcmVuZGVyX2V4YW1fYXVkaXRfY2FyZHMoYXJyYXkgJHNldHMsIGJvb2wgJGlzZXMsIHN0cmluZyAkdHJhY2ssCiAgICAgICAgYXJyYXkgJGxhbmd1YWdlY29udGV4dCwgaW50ICRzZWxlY3RlZCk6IHZvaWQgewogICAgJHRvcGljcyA9IFsKICAgICAgICBbJ0plcmFycXXDrWEgZGUgaW5ncmVkaWVudGVzJywgJ0luZ3JlZGllbnQgaGllcmFyY2h5JywgJ35cYig/OmhpZXJhcmNoXHcqfGplcmFycXVcdyp8ZW1wZXJhZG9yfG1lbnNhamVybylcYnxcYig/OmNoaWVmfGRlcHV0eXxlbnZveXxhc3Npc3RhbnQpXHMrKD86aGVyYnM/fGluZ3JlZGllbnRzP3xyb2xlKXwoPzptYXRjaGVzP3xpZGVudGlmeXxyb2xlfGFzIGFuPylcVysoPzp0aGVcVyspPyg/OmNoaWVmfGRlcHV0eXxhc3Npc3RhbnR8ZW52b3kpXGJ+aXUnXSwKICAgICAgICBbJ0NvbXBvc2ljacOzbiB5IG1vZGlmaWNhY2lvbmVzJywgJ0NvbXBvc2l0aW9uIGFuZCBtb2RpZmljYXRpb25zJywgJ35cYig/OmNvbXBvc2l0aW9ufG1vZGlmXHcqfGFkZCg/OmVkfGluZyk/fHJlbW92ZVx3KnxzdWJzdGl0dXRcdyp8Y29tcG9zaWNpw7NufGFncmVnXHcqfGHDsWFkXHcqfHJldGlyXHcqKVxifml1J10sCiAgICAgICAgWydQcm9jZXNhbWllbnRvIHkgcHJlcGFyYWNpw7NuJywgJ1Byb2Nlc3NpbmcgYW5kIHByZXBhcmF0aW9uJywgJ35cYig/OnByb2Nlc3Ncdyp8cHJlcGFyXHcqfGhvbmV5fHdpbmUucHJlcGFyZWR8Y2hhcnJlZHxyb2FzdGVkfG1pZWx8Y2FyYm9uaXpcdyp8dG9zdGFkXHcqKVxifml1J10sCiAgICAgICAgWydEb3NpcyB5IGFkbWluaXN0cmFjacOzbicsICdEb3NhZ2UgYW5kIGFkbWluaXN0cmF0aW9uJywgJ35cYig/OmRvc2V8ZG9zYWdlfGdyYW1zP3xtaWxsaWdyYW1zP3xkb3Npc3xncmFtb3M/fG1pbGlncmFtb3M/fGRlY29jdFx3KnxkZWNvY2NcdyopXGJ8XGJcZCsoPzpcLlxkKyk/XHMqKD86Z3xtZylcYn5pdSddLAogICAgICAgIFsnQ2FuYWxlcyB5IG1lcmlkaWFub3MnLCAnQ2hhbm5lbHMgYW5kIG1lcmlkaWFucycsICd+XGIoPzpjaGFubmVscz98bWVyaWRpYW5zP3xjYW5hbGVzP3xtZXJpZGlhbm9zPylcYn5pdSddLAogICAgICAgIFsnUHJvcGllZGFkZXMgeSBzYWJvcmVzJywgJ1Byb3BlcnRpZXMgYW5kIGZsYXZvcnMnLCAnflxiKD86dGVtcGVyYXR1cmV8Zmxhdm9bdV0/cnxhY3JpZHxzd2VldHxiaXR0ZXJ8c2FsdHl8c291cnx0ZW1wZXJhdHVyYXxzYWJvcmVzP3xhY3JlfGR1bGNlfGFtYXJnb3xzYWxhZG98w6FjaWRvKVxifml1J10sCiAgICAgICAgWydFbWJhcmF6byB5IGxhY3RhbmNpYScsICdQcmVnbmFuY3kgYW5kIGxhY3RhdGlvbicsICd+XGIoPzpwcmVnbmFuXHcqfGxhY3RhdFx3KnxicmVhc3RmZWVkXHcqfGVtYmFyYXpcdyp8bGFjdGFuY2lhKVxifml1J10sCiAgICAgICAgWydJbnRlcmFjY2lvbmVzJywgJ0ludGVyYWN0aW9ucycsICd+XGIoPzppbnRlcmFjdFx3Knx3YXJmYXJpbnxkaWdveGlufGFudGljb2FndWxcdyp8aW50ZXJhY2Npb25cdyp8bWVkaWNhbWVudG9zPylcYn5pdSddLAogICAgICAgIFsnQ29udHJhaW5kaWNhY2lvbmVzIHkgdG94aWNpZGFkJywgJ0NvbnRyYWluZGljYXRpb25zIGFuZCB0b3hpY2l0eScsICd+XGIoPzpjb250cmFpbmRpY2F0XHcqfHRveGljXHcqfGNhdXRpb258dW5zYWZlfGF2b2lkfHByZWNhdWNpw7NufGV2aXRhcilcYn5pdSddLAogICAgICAgIFsnQ29tcGFyYWNpw7NuIHkgZGlmZXJlbmNpYWNpw7NuJywgJ0NvbXBhcmlzb24gYW5kIGRpZmZlcmVudGlhdGlvbicsICd+XGIoPzpkaXN0aW5ndWlzaFx3KnxkaWZmZXJlbnRpYXRcdyp8Y29tcGFyXHcqfGRpc3RpbmNcdyp8ZGlmZXJlbmNpXHcqKVxifml1J10sCiAgICBdOwogICAgJHJlYWR5ID0gc3RhdGljIGZ1bmN0aW9uKCR2YWx1ZSk6IGJvb2wgewogICAgICAgICR0ZXh0ID0gdHJpbShzdHJpcF90YWdzKChzdHJpbmcpJHZhbHVlKSk7CiAgICAgICAgcmV0dXJuICR0ZXh0ICE9PSAnJyAmJiAhaW5fYXJyYXkoc3RydG9sb3dlcigkdGV4dCksIFsnbm90IGNvbXBsZXRlZCcsICdwZW5kaW5nJywgJ21pc3NpbmcnLCAnbm8gY29tcGxldGFkbycsICdwZW5kaWVudGUnXSwgdHJ1ZSk7CiAgICB9OwogICAgJGNhcmRzID0gW107CiAgICBmb3JlYWNoICgkc2V0cyBhcyAkbnVtYmVyID0+ICRxdWVzdGlvbnMpIHsKICAgICAgICAvLyBPbmx5IGNvbXBsZXRlIHNldHMgb2ZmZXJlZCBieSB0aGUgc3R1ZGVudCBjaG9vc2VyIHF1YWxpZnkuCiAgICAgICAgaWYgKGNvdW50KCRxdWVzdGlvbnMpICE9PSAxMDApIHsgY29udGludWU7IH0KICAgICAgICAkY291bnRzID0gYXJyYXlfZmlsbCgwLCBjb3VudCgkdG9waWNzKSwgMCk7ICRjbGluaWNhbCA9IDA7ICRlc2NvbXBsZXRlID0gMDsKICAgICAgICBmb3JlYWNoICgkcXVlc3Rpb25zIGFzICRxKSB7CiAgICAgICAgICAgICRzdGVtID0gaHRtbF9lbnRpdHlfZGVjb2RlKHN0cmlwX3RhZ3MoKHN0cmluZykoJHFbJ3Byb21wdCddID8/ICcnKSksIEVOVF9RVU9URVMgfCBFTlRfSFRNTDUsICdVVEYtOCcpOwogICAgICAgICAgICBmb3JlYWNoICgkdG9waWNzIGFzICRpID0+ICR0b3BpYykgewogICAgICAgICAgICAgICAgaWYgKCgkaSA9PT0gMCAmJiAoJHFbJ2NvbnRlbnRUeXBlJ10gPz8gJycpID09PSAnZm9ybXVsYV9oaWVyYXJjaHknKSB8fCBwcmVnX21hdGNoKCR0b3BpY1syXSwgJHN0ZW0pKSB7ICRjb3VudHNbJGldKys7IH0KICAgICAgICAgICAgfQogICAgICAgICAgICBpZiAocHJlZ19tYXRjaCgnflxiKD86cGF0aWVudHxwcmVzZW50cz98cGFjaWVudGV8cGVyc29uYSBwcmVzZW50YXx5ZWFyLm9sZHx5ZWFycy5vbGR8YcOxb3MgZGUgZWRhZClcYn5pdScsICRzdGVtKSkgeyAkY2xpbmljYWwrKzsgfQogICAgICAgICAgICAkZXMgPSAkcVsndHJhbnNsYXRpb25zJ11bJ2VzJ10gPz8gW107CiAgICAgICAgICAgICRlc29wdGlvbnMgPSBpc19hcnJheSgkZXNbJ29wdGlvbnMnXSA/PyBudWxsKSA/ICRlc1snb3B0aW9ucyddIDogW107CiAgICAgICAgICAgICRlc3ZhbGlkID0gJHJlYWR5KCRlc1sncHJvbXB0J10gPz8gJycpICYmICRyZWFkeSgkZXNbJ2V4cGxhbmF0aW9uJ10gPz8gJycpICYmIGNvdW50KCRlc29wdGlvbnMpID09PSA0OwogICAgICAgICAgICBmb3JlYWNoICgkZXNvcHRpb25zIGFzICRvKSB7ICRlc3ZhbGlkID0gJGVzdmFsaWQgJiYgaXNfYXJyYXkoJG8pICYmICRyZWFkeSgkb1sndGV4dCddID8/ICcnKTsgfQogICAgICAgICAgICBpZiAoJGVzdmFsaWQpIHsgJGVzY29tcGxldGUrKzsgfQogICAgICAgIH0KICAgICAgICAkY2FyZHNbXSA9IFskbnVtYmVyLCAkY291bnRzLCAkY2xpbmljYWwsICRlc2NvbXBsZXRlXTsKICAgIH0KICAgIGlmICghJGNhcmRzKSB7IHJldHVybjsgfQogICAgPz4KICAgIDxzZWN0aW9uIGNsYXNzPSJleGFtLWF1ZGl0LWNhcmRzIiBhcmlhLWxhYmVsPSI8P3BocCBlY2hvICRpc2VzID8gJ1BlcmZpbCBkZSBjYWRhIGV4YW1lbicgOiAnRWFjaCBleGFtIGF0IGEgZ2xhbmNlJzsgPz4iPgogICAgICAgIDxzdHlsZT4KICAgICAgICAuZXhhbS1hdWRpdC1jYXJkc3ttYXJnaW46MjBweCAwfS5leGFtLWF1ZGl0LWdyaWR7ZGlzcGxheTpncmlkO2dyaWQtdGVtcGxhdGUtY29sdW1uczpyZXBlYXQoYXV0by1maXQsbWlubWF4KDI2NXB4LDFmcikpO2dhcDoxNHB4fS5leGFtLWF1ZGl0LWNhcmR7Ym9yZGVyOjFweCBzb2xpZCAjYmNkMmRmO2JvcmRlci10b3A6NHB4IHNvbGlkICMwMDhlODk7Ym9yZGVyLXJhZGl1czoxMHB4O3BhZGRpbmc6MThweDtiYWNrZ3JvdW5kOiNmZmY7Y29sb3I6IzE3M2Q1MH0uZXhhbS1hdWRpdC1jYXJkIGg0e21hcmdpbjowIDAgMTBweDtmb250LXNpemU6MS4ycmVtfS5leGFtLWF1ZGl0LWNhcmQgcHttYXJnaW46OHB4IDB9LmV4YW0tYXVkaXQtY2FyZCB1bHtwYWRkaW5nLWxlZnQ6MjBweDttYXJnaW46MTBweCAwfS5leGFtLWF1ZGl0LWNhcmQgbGl7bWFyZ2luOjVweCAwfS5leGFtLWF1ZGl0LWNhcmQgYXtkaXNwbGF5OmlubGluZS1ibG9jaztib3JkZXI6MXB4IHNvbGlkICMwMDhlODk7Ym9yZGVyLXJhZGl1czo2cHg7cGFkZGluZzo4cHggMTJweDttYXJnaW4tdG9wOjEwcHg7Y29sb3I6IzAwNmQ2OTtmb250LXdlaWdodDo3MDB9LmV4YW0tYXVkaXQtY2FyZCBkZXRhaWxze21hcmdpbi10b3A6MTJweH0uZXhhbS1hdWRpdC1jYXJkIHN1bW1hcnl7Y3Vyc29yOnBvaW50ZXI7Zm9udC13ZWlnaHQ6NjAwfS5leGFtLWF1ZGl0LWNhcmQgLmV4YW0tYXVkaXQtbm90ZXtmb250LXNpemU6Ljg1cmVtO2xpbmUtaGVpZ2h0OjEuNTtjb2xvcjojNTA2Nzc3fQogICAgICAgIDwvc3R5bGU+CiAgICAgICAgPGgzPjw/cGhwIGVjaG8gJGlzZXMgPyAnRWxpZ2UgZWwgZXhhbWVuIHF1ZSBuZWNlc2l0YXMgcHJhY3RpY2FyJyA6ICdDaG9vc2UgdGhlIGV4YW0geW91IHdhbnQgdG8gcHJhY3RpY2UnOyA/PjwvaDM+CiAgICAgICAgPHA+PD9waHAgZWNobyAkaXNlcyA/ICdQZXJmaWwgZGVsIGNvbmp1bnRvIGRpc3BvbmlibGUgZGUgY2FkYSBleGFtZW4uIExvcyB0ZW1hcyBzZSBkZXRlY3RhbiBlbiBsb3MgZW51bmNpYWRvczsgdW5hIHByZWd1bnRhIHB1ZWRlIGFiYXJjYXIgdmFyaW9zIHRlbWFzLiBFc3RvcyBkYXRvcyBubyBtaWRlbiBsYSBkaWZpY3VsdGFkLicgOiAnUHJvZmlsZSBvZiBlYWNoIGF2YWlsYWJsZSBleGFtIHNldC4gVG9waWNzIGFyZSBkZXRlY3RlZCBpbiBxdWVzdGlvbiBzdGVtczsgb25lIHF1ZXN0aW9uIGNhbiBjb3ZlciBzZXZlcmFsIHRvcGljcy4gVGhlc2UgY291bnRzIGRvIG5vdCBtZWFzdXJlIGRpZmZpY3VsdHkuJzsgPz48L3A+CiAgICAgICAgPGRpdiBjbGFzcz0iZXhhbS1hdWRpdC1ncmlkIj4KICAgICAgICA8P3BocCBmb3JlYWNoICgkY2FyZHMgYXMgWyRudW1iZXIsICRjb3VudHMsICRjbGluaWNhbCwgJGVzY29tcGxldGVdKSB7CiAgICAgICAgICAgICRyYW5rZWQgPSAkY291bnRzOyBhcnNvcnQoJHJhbmtlZCwgU09SVF9OVU1FUklDKTsKICAgICAgICAgICAgJHVybCA9IG5ldyBtb29kbGVfdXJsKCcvYmxvY2tzL21lbnUvaGVyYm9sb2d5LWJvYXJkLnBocCcsIFsndHJhY2snID0+ICR0cmFjaywKICAgICAgICAgICAgICAgICgkdHJhY2sgPT09ICdzaW11bGF0aW9uJyA/ICdzaW11bGF0aW9uJyA6ICdleGFtc2V0JykgPT4gKGludCkkbnVtYmVyXSArICRsYW5ndWFnZWNvbnRleHQpOwogICAgICAgICAgICA/PgogICAgICAgICAgICA8YXJ0aWNsZSBjbGFzcz0iZXhhbS1hdWRpdC1jYXJkIj4KICAgICAgICAgICAgICAgIDxoND48P3BocCBlY2hvICRpc2VzID8gJ0V4YW1lbiAnIDogJ0V4YW0gJzsgZWNobyAoaW50KSRudW1iZXI7ID8+PC9oND4KICAgICAgICAgICAgICAgIDxwPjxzdHJvbmc+MTAwIDw/cGhwIGVjaG8gJGlzZXMgPyAncHJlZ3VudGFzJyA6ICdxdWVzdGlvbnMnOyA/Pjwvc3Ryb25nPjwvcD4KICAgICAgICAgICAgICAgIDxwPjw/cGhwIGVjaG8gJGlzZXMgPyAnQ2Fzb3MgY2zDrW5pY29zIGV4cGzDrWNpdG9zOiAnIDogJ0V4cGxpY2l0IGNsaW5pY2FsIGNhc2VzOiAnOyBlY2hvIChpbnQpJGNsaW5pY2FsOyA/Pi8xMDA8L3A+CiAgICAgICAgICAgICAgICA8cD48P3BocCBlY2hvICRpc2VzID8gJ0plcmFycXXDrWEgZGUgaW5ncmVkaWVudGVzOiAnIDogJ0luZ3JlZGllbnQgaGllcmFyY2h5OiAnOyBlY2hvIChpbnQpJGNvdW50c1swXTsgPz4vMTAwPC9wPgogICAgICAgICAgICAgICAgPHA+PHN0cm9uZz48P3BocCBlY2hvICRpc2VzID8gJ1RlbWFzIG3DoXMgcHJlc2VudGVzJyA6ICdNb3N0IHJlcHJlc2VudGVkIHRvcGljcyc7ID8+PC9zdHJvbmc+PC9wPgogICAgICAgICAgICAgICAgPHVsPjw/cGhwICRzaG93biA9IDA7IGZvcmVhY2ggKCRyYW5rZWQgYXMgJGkgPT4gJGNvdW50KSB7CiAgICAgICAgICAgICAgICAgICAgaWYgKCEkY291bnQgfHwgJHNob3duID49IDMpIHsgY29udGludWU7IH0gJHNob3duKys7CiAgICAgICAgICAgICAgICAgICAgPz48bGk+PD9waHAgZWNobyBzKCR0b3BpY3NbJGldWyRpc2VzID8gMCA6IDFdKTsgPz46IDxzdHJvbmc+PD9waHAgZWNobyAoaW50KSRjb3VudDsgPz48L3N0cm9uZz48L2xpPjw/cGhwCiAgICAgICAgICAgICAgICB9IGlmICghJHNob3duKSB7ID8+PGxpPjw/cGhwIGVjaG8gJGlzZXMgPyAnU2luIHRlbWFzIGVzcGVjw61maWNvcyBkZXRlY3RhZG9zJyA6ICdObyBzcGVjaWZpYyB0b3BpY3MgZGV0ZWN0ZWQnOyA/PjwvbGk+PD9waHAgfSA/PjwvdWw+CiAgICAgICAgICAgICAgICA8ZGV0YWlscz48c3VtbWFyeT48P3BocCBlY2hvICRpc2VzID8gJ1ZlciB0b2RvcyBsb3MgdGVtYXMnIDogJ1ZpZXcgYWxsIHRvcGljcyc7ID8+PC9zdW1tYXJ5PgogICAgICAgICAgICAgICAgPHVsPjw/cGhwIGZvcmVhY2ggKCR0b3BpY3MgYXMgJGkgPT4gJHRvcGljKSB7ID8+PGxpPjw/cGhwIGVjaG8gcygkdG9waWNbJGlzZXMgPyAwIDogMV0pOyA/PjogPD9waHAgZWNobyAoaW50KSRjb3VudHNbJGldOyA/PjwvbGk+PD9waHAgfSA/PjwvdWw+PC9kZXRhaWxzPgogICAgICAgICAgICAgICAgPHAgY2xhc3M9ImV4YW0tYXVkaXQtbm90ZSI+PD9waHAgZWNobyAkaXNlcyA/ICdFc3Bhw7FvbDogJyA6ICdTcGFuaXNoOiAnOyBlY2hvIChpbnQpJGVzY29tcGxldGU7ID8+LzEwMCA8P3BocCBlY2hvICRpc2VzID8gJ3ByZWd1bnRhcyBjb24gZW51bmNpYWRvLCBvcGNpb25lcyB5IGV4cGxpY2FjacOzbiBjb21wbGV0b3MuJyA6ICdxdWVzdGlvbnMgd2l0aCBjb21wbGV0ZSBzdGVtLCBjaG9pY2VzIGFuZCBleHBsYW5hdGlvbi4nOyA/PjwvcD4KICAgICAgICAgICAgICAgIDxhIGhyZWY9Ijw/cGhwIGVjaG8gcygkdXJsLT5vdXQoZmFsc2UpKTsgPz4iPD9waHAgZWNobyAoaW50KSRudW1iZXIgPT09ICRzZWxlY3RlZCA/ICcgYXJpYS1jdXJyZW50PSJwYWdlIicgOiAnJzsgPz4+PD9waHAgZWNobyAkaXNlcyA/ICdFbGVnaXIgZXhhbWVuICcgOiAnQ2hvb3NlIEV4YW0gJzsgZWNobyAoaW50KSRudW1iZXI7ID8+PC9hPgogICAgICAgICAgICA8L2FydGljbGU+CiAgICAgICAgPD9waHAgfSA/PgogICAgICAgIDwvZGl2PgogICAgPC9zZWN0aW9uPgogICAgPD9waHAKfQo='))
PYTHON
php -l "$TEMP_DIR/page.php"
php -l "$TEMP_DIR/helper.php"
cat > "$TEMP_DIR/check.php" <<'PHP'
<?php
error_reporting(E_ALL);
set_error_handler(static function($level,$message){throw new RuntimeException($message);});
define('MOODLE_INTERNAL', true);
function s($value){return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8');}
class moodle_url {
    private $url;
    public function __construct($path,$params){$this->url=$path.'?'.http_build_query($params);}
    public function out($escaped=true){return $escaped?s($this->url):$this->url;}
}
require __DIR__.'/helper.php';
$q=['prompt'=>'Patient presents with cough. Which option matches Envoy?', 'contentType'=>'formula_hierarchy', 'options'=>[], 'correctLabel'=>'A', 'explanation'=>'SECRET-ANSWER'];
$es=['prompt'=>'Paciente con tos.', 'explanation'=>'Explicación.', 'options'=>array_fill(0,4,['text'=>'Opción'])];
$q['translations']['es']=$es;
$sets=[1=>array_fill(0,100,$q),8=>array_fill(0,77,$q)];
foreach([false,true] as $spanish){
    foreach(['simulation','exams'] as $track){
        ob_start();block_myprogress_render_exam_audit_cards($sets,$spanish,$track,['lang'=>$spanish?'es':'en'],1);$html=ob_get_clean();
        foreach(['SECRET-ANSWER','Exam 8','Examen 8','exam=linear'] as $bad){if(strpos($html,$bad)!==false){throw new RuntimeException('Unexpected output: '.$bad);}}
        foreach(['100/100',($track==='simulation'?'simulation=1':'examset=1'),'aria-current="page"',($spanish?'Elegir examen 1':'Choose Exam 1')] as $good){if(strpos($html,$good)===false){throw new RuntimeException('Missing output: '.$good);}}
    }
}
$sets[1][0]['translations']['es']['options'][0]['text']='';
ob_start();block_myprogress_render_exam_audit_cards($sets,true,'exams',[],1);$html=ob_get_clean();
if(strpos($html,'99/100')===false){throw new RuntimeException('Incomplete Spanish not detected');}
echo "OK: tarjetas EN/ES, métricas, exclusión de exámenes incompletos y enlaces de selección.\n";
PHP
php "$TEMP_DIR/check.php"
BACKUP="${BACKUP_ROOT:-/var/backups/acupuncturetests}/student-exam-audit-cards-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
cp -a "$TARGET" "$BACKUP/herbology-board.php"
if [ -e "$HELPER" ]; then cp -a "$HELPER" "$BACKUP/exam-audit-student-cards.php"; fi
OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
MODE="$(stat -c '%a' "$TARGET")"
if ! install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/helper.php" "$HELPER" || ! install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/page.php" "$TARGET"; then
    cp -a "$BACKUP/herbology-board.php" "$TARGET"
    if [ -f "$BACKUP/exam-audit-student-cards.php" ]; then cp -a "$BACKUP/exam-audit-student-cards.php" "$HELPER"; else rm -f "$HELPER"; fi
    echo 'ERROR: se restauraron los archivos anteriores.' >&2
    exit 1
fi
echo 'OK: una tarjeta de auditoría por examen completo disponible, en ambos selectores del alumno.'
echo 'OK: métricas actuales EN/ES, tipos de pregunta, temas y enlace para elegir el examen.'
echo 'No se modificaron preguntas, publicaciones ni intentos.'
echo "RESPALDO: $BACKUP"
